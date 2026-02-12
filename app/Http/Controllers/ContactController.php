<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Contact Controller
 *
 * Handles public contact form submissions.
 */
class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function show(): View
    {
        return view('contact.show');
    }

    /**
     * Store a new contact submission.
     *
     * Creates a contact record and redirects with success message.
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        $contact = Contact::create($request->contactData());

        // Optionally, you could dispatch a notification job here
        // ContactNotificationJob::dispatch($contact);

        return redirect()
            ->route('contact.show')
            ->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
