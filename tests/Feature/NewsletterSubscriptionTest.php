<?php

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

describe('Newsletter Subscription Feature', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
    });

    describe('public API endpoints', function () {
        it('can subscribe to newsletter with valid data', function () {
            $response = $this->postJson(route('api.newsletter.subscribe'), [
                'email' => 'subscriber@example.com',
                'name' => 'John Doe',
            ]);

            $response->assertStatus(201);
            $response->assertJson([
                'message' => 'Thank you for subscribing! Please check your email to confirm your subscription.',
            ]);

            $this->assertDatabaseHas('newsletter_subscribers', [
                'email' => 'subscriber@example.com',
                'name' => 'John Doe',
                'status' => 'pending',
            ]);
        });

        it('can subscribe without name', function () {
            $response = $this->postJson(route('api.newsletter.subscribe'), [
                'email' => 'subscriber@example.com',
            ]);

            $response->assertStatus(201);

            $this->assertDatabaseHas('newsletter_subscribers', [
                'email' => 'subscriber@example.com',
                'name' => null,
            ]);
        });

        it('validates email is required', function () {
            $response = $this->postJson(route('api.newsletter.subscribe'), []);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['email']);
        });

        it('validates email format', function () {
            $response = $this->postJson(route('api.newsletter.subscribe'), [
                'email' => 'invalid-email',
            ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['email']);
        });

        it('reactivates soft-deleted subscriber', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'name' => 'John Doe',
                'status' => 'unsubscribed',
            ]);
            $subscriber->delete();

            $response = $this->postJson(route('api.newsletter.subscribe'), [
                'email' => 'subscriber@example.com',
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(201);

            $this->assertDatabaseHas('newsletter_subscribers', [
                'email' => 'subscriber@example.com',
                'name' => 'Updated Name',
                'deleted_at' => null,
            ]);
        });

        it('returns 200 for already confirmed active subscriber', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'status' => 'active',
                'confirmed_at' => now(),
            ]);

            $response = $this->postJson(route('api.newsletter.subscribe'), [
                'email' => 'subscriber@example.com',
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'You are already subscribed to our newsletter.',
            ]);
        });

        it('can confirm subscription with valid token', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'status' => 'pending',
                'confirmation_token' => 'valid-token-123',
            ]);

            $response = $this->getJson(route('api.newsletter.confirm', ['token' => 'valid-token-123']));

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'Your subscription has been confirmed. Thank you!',
            ]);

            $this->assertDatabaseHas('newsletter_subscribers', [
                'email' => 'subscriber@example.com',
                'status' => 'active',
            ]);

            expect($subscriber->fresh()->isConfirmed())->toBeTrue();
        });

        it('returns 404 for invalid confirmation token', function () {
            $response = $this->getJson(route('api.newsletter.confirm', ['token' => 'invalid-token']));

            $response->assertStatus(404);
        });

        it('returns 200 for already confirmed subscriber', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'status' => 'active',
                'confirmed_at' => now(),
                'confirmation_token' => 'valid-token',
            ]);

            $response = $this->getJson(route('api.newsletter.confirm', ['token' => 'valid-token']));

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'Your subscription is already confirmed.',
            ]);
        });

        it('can unsubscribe with email', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'status' => 'active',
                'confirmed_at' => now(),
            ]);

            $response = $this->postJson(route('api.newsletter.unsubscribe'), [
                'email' => 'subscriber@example.com',
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'You have been successfully unsubscribed from our newsletter.',
            ]);

            expect($subscriber->fresh()->isUnsubscribed())->toBeTrue();
        });

        it('returns 422 when email is missing for unsubscribe', function () {
            $response = $this->postJson(route('api.newsletter.unsubscribe'), []);

            $response->assertStatus(422);
        });

        it('returns 404 for non-existent subscriber on unsubscribe', function () {
            $response = $this->postJson(route('api.newsletter.unsubscribe'), [
                'email' => 'nonexistent@example.com',
            ]);

            $response->assertStatus(404);
        });

        it('returns 200 for already unsubscribed user', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            $response = $this->postJson(route('api.newsletter.unsubscribe'), [
                'email' => 'subscriber@example.com',
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'You are already unsubscribed.',
            ]);
        });

        it('can check subscription status', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'subscriber@example.com',
                'status' => 'active',
                'confirmed_at' => now(),
            ]);

            $response = $this->getJson(route('api.newsletter.status', ['email' => 'subscriber@example.com']));

            $response->assertStatus(200);
            $response->assertJson([
                'subscribed' => true,
                'confirmed' => true,
                'status' => 'active',
                'email' => 'subscriber@example.com',
            ]);
        });

        it('returns 404 for non-existent subscriber on status check', function () {
            $response = $this->getJson(route('api.newsletter.status', ['email' => 'nonexistent@example.com']));

            $response->assertStatus(404);
            $response->assertJson([
                'subscribed' => false,
            ]);
        });

        it('captures IP address on subscription', function () {
            $response = $this->withServerVariables([
                'REMOTE_ADDR' => '192.168.1.1',
            ])->postJson(route('api.newsletter.subscribe'), [
                'email' => 'subscriber@example.com',
            ]);

            $response->assertStatus(201);

            $this->assertDatabaseHas('newsletter_subscribers', [
                'email' => 'subscriber@example.com',
                'ip_address' => '192.168.1.1',
            ]);
        });
    });

    describe('newsletter subscriber model', function () {
        it('can confirm subscriber', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'status' => 'pending',
            ]);

            expect($subscriber->isConfirmed())->toBeFalse();

            $subscriber->confirm();

            expect($subscriber->fresh()->isConfirmed())->toBeTrue();
            expect($subscriber->fresh()->status)->toBe('active');
        });

        it('can unsubscribe subscriber', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'status' => 'active',
                'confirmed_at' => now(),
            ]);

            $subscriber->unsubscribe();

            expect($subscriber->fresh()->isUnsubscribed())->toBeTrue();
            expect($subscriber->fresh()->unsubscribed_at)->not->toBeNull();
        });

        it('can reactivate subscriber', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            $subscriber->reactivate();

            expect($subscriber->fresh()->isActive())->toBeTrue();
            expect($subscriber->fresh()->unsubscribed_at)->toBeNull();
        });

        it('scopes active subscribers', function () {
            NewsletterSubscriber::create(['email' => 'active@example.com', 'status' => 'active']);
            NewsletterSubscriber::create(['email' => 'pending@example.com', 'status' => 'pending']);
            NewsletterSubscriber::create(['email' => 'unsubscribed@example.com', 'status' => 'unsubscribed']);

            $activeCount = NewsletterSubscriber::active()->count();

            expect($activeCount)->toBe(1);
        });

        it('scopes pending subscribers', function () {
            NewsletterSubscriber::create(['email' => 'active@example.com', 'status' => 'active']);
            NewsletterSubscriber::create(['email' => 'pending@example.com', 'status' => 'pending']);

            $pendingCount = NewsletterSubscriber::pending()->count();

            expect($pendingCount)->toBe(1);
        });

        it('scopes confirmed subscribers', function () {
            NewsletterSubscriber::create(['email' => 'confirmed@example.com', 'status' => 'active', 'confirmed_at' => now()]);
            NewsletterSubscriber::create(['email' => 'unconfirmed@example.com', 'status' => 'pending']);

            $confirmedCount = NewsletterSubscriber::confirmed()->count();

            expect($confirmedCount)->toBe(1);
        });

        it('generates confirmation token on create', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
            ]);

            expect($subscriber->confirmation_token)->not->toBeNull();
            expect(strlen($subscriber->confirmation_token))->toBe(64);
        });

        it('sets subscribed_at on create', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
            ]);

            expect($subscriber->subscribed_at)->not->toBeNull();
        });

        it('can find by token', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'confirmation_token' => 'test-token-123',
            ]);

            $found = NewsletterSubscriber::findByToken('test-token-123');

            expect($found)->not->toBeNull();
            expect($found->id)->toBe($subscriber->id);
        });

        it('returns display name', function () {
            $subscriberWithName = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'name' => 'John Doe',
            ]);

            $subscriberWithoutName = NewsletterSubscriber::create([
                'email' => 'test2@example.com',
            ]);

            expect($subscriberWithName->displayName())->toBe('John Doe');
            expect($subscriberWithoutName->displayName())->toBe('test2@example.com');
        });
    });

    describe('admin routes', function () {
        beforeEach(function () {
            $this->admin = User::factory()->create();
            $this->admin->assignRole('Admin');
        });

        it('can view subscribers list', function () {
            NewsletterSubscriber::create(['email' => 'test@example.com', 'status' => 'active']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.newsletter-subscribers.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.newsletter-subscribers.index');
        });

        it('can view subscriber details', function () {
            $subscriber = NewsletterSubscriber::create(['email' => 'test@example.com', 'status' => 'active']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.newsletter-subscribers.show', $subscriber));

            $response->assertStatus(200);
            $response->assertViewIs('admin.newsletter-subscribers.show');
        });

        it('can update subscriber', function () {
            $subscriber = NewsletterSubscriber::create(['email' => 'test@example.com', 'status' => 'pending']);

            $response = $this->actingAs($this->admin)
                ->put(route('admin.newsletter-subscribers.update', $subscriber), [
                    'name' => 'Updated Name',
                    'status' => 'active',
                ]);

            $response->assertRedirect(route('admin.newsletter-subscribers.index'));
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('newsletter_subscribers', [
                'id' => $subscriber->id,
                'name' => 'Updated Name',
                'status' => 'active',
            ]);
        });

        it('can confirm subscriber via admin', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'status' => 'pending',
            ]);

            $response = $this->actingAs($this->admin)
                ->put(route('admin.newsletter-subscribers.confirm', $subscriber));

            $response->assertRedirect();
            $response->assertSessionHas('success');

            expect($subscriber->fresh()->isConfirmed())->toBeTrue();
        });

        it('can unsubscribe subscriber via admin', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'status' => 'active',
                'confirmed_at' => now(),
            ]);

            $response = $this->actingAs($this->admin)
                ->put(route('admin.newsletter-subscribers.unsubscribe', $subscriber));

            $response->assertRedirect();
            $response->assertSessionHas('success');

            expect($subscriber->fresh()->isUnsubscribed())->toBeTrue();
        });

        it('can reactivate subscriber via admin', function () {
            $subscriber = NewsletterSubscriber::create([
                'email' => 'test@example.com',
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            $response = $this->actingAs($this->admin)
                ->put(route('admin.newsletter-subscribers.reactivate', $subscriber));

            $response->assertRedirect();
            $response->assertSessionHas('success');

            expect($subscriber->fresh()->isActive())->toBeTrue();
        });

        it('can delete subscriber', function () {
            $subscriber = NewsletterSubscriber::create(['email' => 'test@example.com']);

            $response = $this->actingAs($this->admin)
                ->delete(route('admin.newsletter-subscribers.destroy', $subscriber));

            $response->assertRedirect(route('admin.newsletter-subscribers.index'));
            $response->assertSessionHas('success');

            $this->assertSoftDeleted('newsletter_subscribers', [
                'id' => $subscriber->id,
            ]);
        });
    });
});
