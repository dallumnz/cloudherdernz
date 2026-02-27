<?php

namespace App\Livewire;

use App\Mail\SubscriberConfirmation;
use App\Models\NewsletterSubscriber;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscribe extends Component
{
    public string $email = '';

    public string $message = '';

    public string $messageType = 'success';

    protected array $rules = [
        'email' => 'required|email|max:255',
    ];

    public function subscribe(): void
    {
        $this->validate();

        // Check if already subscribed
        $existing = NewsletterSubscriber::where('email', $this->email)->first();

        if ($existing) {
            if ($existing->status === 'unsubscribed') {
                $existing->reactivate();
                $this->message = 'Welcome back! You have been re-subscribed.';
                $this->messageType = 'success';
            } elseif ($existing->status === 'pending') {
                // Resend confirmation email
                Mail::to($existing->email)->send(new SubscriberConfirmation($existing));
                $this->message = 'Confirmation email sent again! Please check your inbox.';
                $this->messageType = 'success';
            } else {
                $this->message = 'This email is already subscribed!';
                $this->messageType = 'error';
            }
        } else {
            // Create new subscriber
            $subscriber = NewsletterSubscriber::create([
                'email' => $this->email,
                'status' => 'pending',
            ]);

            // Send confirmation email
            Mail::to($subscriber->email)->send(new SubscriberConfirmation($subscriber));

            $this->message = 'Thanks for subscribing! Check your email to confirm.';
            $this->messageType = 'success';
        }

        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.newsletter-subscribe');
    }
}
