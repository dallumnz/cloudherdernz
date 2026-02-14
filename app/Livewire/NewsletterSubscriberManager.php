<?php

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterSubscriberManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $email = '';

    public string $name = '';

    public string $status = 'active';

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    protected array $rules = [
        'email' => 'required|email|max:255',
        'name' => 'nullable|string|max:255',
        'status' => 'required|in:active,pending,unsubscribed',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit(int $id): void
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);

        $this->editingId = $subscriber->id;
        $this->email = $subscriber->email;
        $this->name = $subscriber->name ?? '';
        $this->status = $subscriber->status;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            $subscriber = NewsletterSubscriber::findOrFail($this->editingId);
            $subscriber->update([
                'email' => $this->email,
                'name' => $this->name ?: null,
                'status' => $this->status,
            ]);
            $this->setMessage('Subscriber updated successfully.');
        } else {
            NewsletterSubscriber::create([
                'email' => $this->email,
                'name' => $this->name ?: null,
                'status' => $this->status,
            ]);
            $this->setMessage('Subscriber created successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();
        $this->setMessage('Subscriber deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->email = '';
        $this->name = '';
        $this->status = 'active';
        $this->editingId = null;
        $this->showForm = false;
    }

    protected function setMessage(string $message, string $type = 'success'): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($this->search, function ($query) {
                $query->where('email', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->latest('subscribed_at')
            ->paginate(15);

        $counts = [
            'all' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::where('status', 'active')->count(),
            'pending' => NewsletterSubscriber::where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
        ];

        return view('livewire.newsletter-subscriber-manager', compact('subscribers', 'counts'));
    }
}
