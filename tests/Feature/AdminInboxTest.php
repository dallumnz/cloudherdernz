<?php

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

describe('Admin Inbox Feature', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->editor = User::factory()->create();
        $this->editor->assignRole('Editor');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('Viewer');
    });

    describe('inbox access', function () {
        it('allows admin to access inbox', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.inbox.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.inbox.index');
        });

        it('allows editor with view contacts permission to access inbox', function () {
            // Editor doesn't have view contacts by default, let's give it
            $this->editor->givePermissionTo('view contacts');

            $response = $this->actingAs($this->editor)
                ->get(route('admin.inbox.index'));

            $response->assertStatus(200);
        });

        it('denies viewer without view contacts permission', function () {
            $response = $this->actingAs($this->viewer)
                ->get(route('admin.inbox.index'));

            $response->assertStatus(403);
        });

        it('redirects guest to login', function () {
            $response = $this->get(route('admin.inbox.index'));

            $response->assertRedirect(route('login'));
        });
    });

    describe('inbox listing', function () {
        it('displays contacts in inbox', function () {
            Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Test Subject',
                'message' => 'Test message',
                'status' => 'unread',
            ]);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.inbox.index'));

            $response->assertStatus(200);
            $response->assertViewHas('contacts');
            $response->assertSee('Test Subject');
        });

        it('filters contacts by status', function () {
            Contact::create(['name' => 'Unread Contact', 'email' => 'a@test.com', 'message' => 'Test', 'status' => 'unread']);
            Contact::create(['name' => 'Read Contact', 'email' => 'b@test.com', 'message' => 'Test', 'status' => 'read']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.inbox.index', ['status' => 'unread']));

            $response->assertStatus(200);
            $response->assertSee('Unread Contact');
            $response->assertDontSee('Read Contact');
        });

        it('shows correct counts for each status', function () {
            Contact::create(['name' => 'A', 'email' => 'a@test.com', 'message' => 'Test', 'status' => 'unread']);
            Contact::create(['name' => 'B', 'email' => 'b@test.com', 'message' => 'Test', 'status' => 'read']);
            Contact::create(['name' => 'C', 'email' => 'c@test.com', 'message' => 'Test', 'status' => 'archived']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.inbox.index'));

            $response->assertStatus(200);
            $response->assertViewHas('counts', function ($counts) {
                return $counts['all'] === 3
                    && $counts['unread'] === 1
                    && $counts['read'] === 1
                    && $counts['archived'] === 1;
            });
        });
    });

    describe('viewing contact details', function () {
        it('can view contact details', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Test Subject',
                'message' => 'Test message',
                'status' => 'unread',
            ]);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.inbox.show', $contact));

            $response->assertStatus(200);
            $response->assertViewIs('admin.inbox.show');
            $response->assertViewHas('contact');
        });

        it('auto-marks contact as read when viewing', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
                'status' => 'unread',
            ]);

            expect($contact->isUnread())->toBeTrue();

            $this->actingAs($this->admin)
                ->get(route('admin.inbox.show', $contact));

            expect($contact->fresh()->isRead())->toBeTrue();
            expect($contact->fresh()->read_at)->not->toBeNull();
        });
    });

    describe('marking as read', function () {
        it('can mark contact as read', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
                'status' => 'unread',
            ]);

            $response = $this->actingAs($this->admin)
                ->put(route('admin.inbox.read', $contact));

            $response->assertRedirect();
            $response->assertSessionHas('success');

            expect($contact->fresh()->isRead())->toBeTrue();
        });

        it('requires manage contacts permission to mark as read', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
                'status' => 'unread',
            ]);

            // Editor without manage contacts permission
            $this->editor->givePermissionTo('view contacts');

            $response = $this->actingAs($this->editor)
                ->put(route('admin.inbox.read', $contact));

            $response->assertStatus(403);
        });
    });

    describe('marking as spam', function () {
        it('can mark contact as spam and block sender', function () {
            $contact = Contact::create([
                'name' => 'Spammer',
                'email' => 'spam@example.com',
                'message' => 'Spam message',
                'status' => 'unread',
            ]);

            $response = $this->actingAs($this->admin)
                ->put(route('admin.inbox.spam', $contact));

            $response->assertRedirect(route('admin.inbox.index'));
            $response->assertSessionHas('success');

            // Contact should be soft deleted
            $this->assertSoftDeleted('contacts', ['id' => $contact->id]);

            // Email should be blocked
            $this->assertDatabaseHas('contact_blocklist', [
                'type' => 'email',
                'value' => 'spam@example.com',
            ]);
        });

        it('requires manage contacts permission to mark as spam', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
            ]);

            $this->editor->givePermissionTo('view contacts');

            $response = $this->actingAs($this->editor)
                ->put(route('admin.inbox.spam', $contact));

            $response->assertStatus(403);
        });
    });

    describe('deleting contacts', function () {
        it('can delete a contact', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
            ]);

            $response = $this->actingAs($this->admin)
                ->delete(route('admin.inbox.destroy', $contact));

            $response->assertRedirect(route('admin.inbox.index'));
            $response->assertSessionHas('success');

            $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
        });

        it('requires delete contacts permission to delete', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
            ]);

            // Editor has view but not delete
            $this->editor->givePermissionTo('view contacts');

            $response = $this->actingAs($this->editor)
                ->delete(route('admin.inbox.destroy', $contact));

            $response->assertStatus(403);
        });
    });
});
