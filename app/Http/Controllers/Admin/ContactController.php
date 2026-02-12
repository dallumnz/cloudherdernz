<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactBlocklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin Contact Controller
 *
 * Handles contact inbox management for administrators.
 */
class ContactController extends Controller
{
    /**
     * Display a listing of contacts.
     *
     * Shows inbox with filtering by status.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $contacts = Contact::query()
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $counts = [
            'all' => Contact::count(),
            'unread' => Contact::unread()->count(),
            'read' => Contact::read()->count(),
            'archived' => Contact::archived()->count(),
        ];

        return view('admin.inbox.index', compact('contacts', 'counts', 'status'));
    }

    /**
     * Display the specified contact.
     *
     * Automatically marks the contact as read when viewed.
     */
    public function show(Contact $contact): View
    {
        // Auto-mark as read when viewing
        if ($contact->isUnread()) {
            $contact->markAsRead();
        }

        return view('admin.inbox.show', compact('contact'));
    }

    /**
     * Mark the contact as read.
     */
    public function markAsRead(Contact $contact): RedirectResponse
    {
        $this->authorize('manage contacts');

        $contact->markAsRead();

        return redirect()
            ->back()
            ->with('success', 'Contact marked as read.');
    }

    /**
     * Mark the contact as spam and block the sender.
     */
    public function markAsSpam(Contact $contact): RedirectResponse
    {
        $this->authorize('manage contacts');

        // Block the sender's email
        ContactBlocklist::blockEmail($contact->email, 'Marked as spam from contact #'.$contact->id);

        // Delete the contact
        $contact->delete();

        return redirect()
            ->route('admin.inbox.index')
            ->with('success', 'Contact marked as spam and sender has been blocked.');
    }

    /**
     * Remove the specified contact.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('delete contacts');

        $contact->delete();

        return redirect()
            ->route('admin.inbox.index')
            ->with('success', 'Contact deleted successfully.');
    }
}
