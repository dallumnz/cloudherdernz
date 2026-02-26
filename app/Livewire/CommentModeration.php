<?php

namespace App\Livewire;

use App\Models\Comment;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CommentModeration extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'pending';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function approve(Comment $comment): void
    {
        $comment->update(['is_approved' => true]);
        session()->flash('message', 'Comment approved successfully.');
    }

    public function reject(Comment $comment): void
    {
        $comment->delete();
        session()->flash('message', 'Comment rejected and removed.');
    }

    public function render(): View
    {
        $query = Comment::with(['user', 'commentable']);

        // Apply filter
        match ($this->filter) {
            'pending' => $query->where('is_approved', false),
            'approved' => $query->where('is_approved', true),
            'rejected' => $query->onlyTrashed(),
            default => $query,
        };

        // Apply search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('body', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => Comment::where('is_approved', false)->count(),
            'approved' => Comment::where('is_approved', true)->count(),
            'rejected' => Comment::onlyTrashed()->count(),
        ];

        return view('livewire.comment-moderation', [
            'comments' => $comments,
            'stats' => $stats,
        ]);
    }
}
