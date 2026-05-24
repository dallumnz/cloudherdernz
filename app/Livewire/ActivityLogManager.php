<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $causerId = null;

    public string $event = '';

    public string $subjectType = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCauserId(): void
    {
        $this->resetPage();
    }

    public function updatedEvent(): void
    {
        $this->resetPage();
    }

    public function updatedSubjectType(): void
    {
        $this->resetPage();
    }

    public function getCausersProperty()
    {
        $userIds = Activity::query()
            ->where('causer_type', User::class)
            ->whereNotNull('causer_id')
            ->distinct()
            ->pluck('causer_id');

        return User::query()
            ->whereIn('id', $userIds)
            ->orderBy('name')
            ->get();
    }

    public function getEventsProperty(): array
    {
        return ['created', 'updated', 'deleted'];
    }

    public function getSubjectTypesProperty(): array
    {
        return [
            'App\Models\User' => 'User',
            'App\Models\Post' => 'Post',
            'App\Models\Page' => 'Page',
            'App\Models\Comment' => 'Comment',
            'App\Models\NewsletterPost' => 'Newsletter Post',
            'App\Models\NewsletterSubscriber' => 'Newsletter Subscriber',
        ];
    }

    public function render(): View
    {
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->latest();

        if ($this->search) {
            $query->where('description', 'like', "%{$this->search}%");
        }

        if ($this->causerId) {
            $query->where('causer_id', $this->causerId)
                ->where('causer_type', User::class);
        }

        if ($this->event) {
            $query->where('event', $this->event);
        }

        if ($this->subjectType) {
            $query->where('subject_type', $this->subjectType);
        }

        /** @var LengthAwarePaginator $activities */
        $activities = $query->paginate(25);

        return view('livewire.activity-log-manager', [
            'activities' => $activities,
        ]);
    }
}
