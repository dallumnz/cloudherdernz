<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function confirm(string $token)
    {
        $subscriber = NewsletterSubscriber::findByToken($token);

        if (!$subscriber) {
            return view('emails.confirmation-invalid');
        }

        if ($subscriber->status === 'active') {
            return view('emails.confirmation-already-confirmed');
        }

        $subscriber->confirm();

        return view('emails.confirmation-success');
    }

    public function showUnsubscribe(Request $request)
    {
        $email = $request->get('email');
        return view('newsletters.unsubscribe', compact('email'));
    }
}
