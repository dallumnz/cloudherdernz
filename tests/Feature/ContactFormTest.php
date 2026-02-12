<?php

use App\Models\Contact;
use App\Models\ContactBlocklist;
use Database\Seeders\RolePermissionSeeder;

describe('Contact Form Feature', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
    });

    describe('public contact form', function () {
        it('can display the contact form', function () {
            $response = $this->get(route('contact.show'));

            $response->assertStatus(200);
            $response->assertViewIs('contact.show');
        });

        it('can submit a contact form with valid data', function () {
            $response = $this->post(route('contact.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Test Subject',
                'message' => 'This is a test message.',
                'h-captcha-response' => 'test-captcha-token',
            ]);

            $response->assertRedirect(route('contact.show'));
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('contacts', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Test Subject',
                'message' => 'This is a test message.',
                'status' => 'unread',
            ]);
        });

        it('can submit a contact form without subject', function () {
            $response = $this->post(route('contact.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'message' => 'This is a test message without subject.',
                'h-captcha-response' => 'test-captcha-token',
            ]);

            $response->assertRedirect(route('contact.show'));
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('contacts', [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'subject' => null,
                'message' => 'This is a test message without subject.',
            ]);
        });

        it('validates required fields', function () {
            $response = $this->post(route('contact.store'), []);

            $response->assertSessionHasErrors(['name', 'email', 'message']);
        });

        it('validates email format', function () {
            $response = $this->post(route('contact.store'), [
                'name' => 'John Doe',
                'email' => 'invalid-email',
                'message' => 'Test message',
                'h-captcha-response' => 'test-captcha-token',
            ]);

            $response->assertSessionHasErrors(['email']);
        });

        it('validates maximum message length', function () {
            $response = $this->post(route('contact.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => str_repeat('a', 5001),
                'h-captcha-response' => 'test-captcha-token',
            ]);

            $response->assertSessionHasErrors(['message']);
        });

        it('captures sender ip address', function () {
            $response = $this->withServerVariables([
                'REMOTE_ADDR' => '192.168.1.1',
            ])->post(route('contact.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
                'h-captcha-response' => 'test-captcha-token',
            ]);

            $response->assertRedirect();

            $this->assertDatabaseHas('contacts', [
                'name' => 'John Doe',
                'sender_ip' => '192.168.1.1',
            ]);
        });

        it('silently rejects blocked emails', function () {
            // Block the email first
            ContactBlocklist::blockEmail('blocked@example.com', 'Test block');

            $response = $this->post(route('contact.store'), [
                'name' => 'Blocked User',
                'email' => 'blocked@example.com',
                'message' => 'This should be blocked.',
                'h-captcha-response' => 'test-captcha-token',
            ]);

            $response->assertRedirect();

            // Should still create a contact but with modified data
            $this->assertDatabaseHas('contacts', [
                'email' => 'blocked@example.com',
            ]);
        });
    });

    describe('contact model', function () {
        it('can mark contact as read', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
                'status' => 'unread',
            ]);

            expect($contact->isUnread())->toBeTrue();

            $contact->markAsRead();

            expect($contact->fresh()->isRead())->toBeTrue();
            expect($contact->fresh()->read_at)->not->toBeNull();
        });

        it('can mark contact as archived', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'Test message',
                'status' => 'read',
            ]);

            $contact->markAsArchived();

            expect($contact->fresh()->isArchived())->toBeTrue();
        });

        it('generates message preview', function () {
            $contact = Contact::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => str_repeat('a', 200),
            ]);

            $preview = $contact->messagePreview();

            expect($preview)->toHaveLength(103); // 100 chars + '...'
            expect(str($preview)->endsWith('...'))->toBeTrue();
        });

        it('scopes unread contacts', function () {
            Contact::create(['name' => 'Unread', 'email' => 'unread@test.com', 'message' => 'Test', 'status' => 'unread']);
            Contact::create(['name' => 'Read', 'email' => 'read@test.com', 'message' => 'Test', 'status' => 'read']);
            Contact::create(['name' => 'Archived', 'email' => 'archived@test.com', 'message' => 'Test', 'status' => 'archived']);

            $unreadCount = Contact::unread()->count();

            expect($unreadCount)->toBe(1);
        });
    });
});
