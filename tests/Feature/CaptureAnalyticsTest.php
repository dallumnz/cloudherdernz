<?php

use App\Http\Middleware\CaptureAnalytics;
use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Support\Facades\Route;

describe('Capture Analytics Middleware', function () {
    beforeEach(function () {
        // Define test routes in the test environment
        Route::middleware(CaptureAnalytics::class)
            ->get('/test-analytics', fn () => 'OK')
            ->name('test.analytics');

        Route::middleware(CaptureAnalytics::class)
            ->get('/admin/test', fn () => 'Admin OK')
            ->name('admin.test');
    });

    it('captures analytics for public routes', function () {
        $response = $this->get('/test-analytics');

        $response->assertStatus(200);
        $this->assertDatabaseHas('analytics_events', [
            'url' => url('/test-analytics'),
        ]);
    });

    it('captures URL, user_id, ip_address, user_agent, and referrer', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders([
                'User-Agent' => 'TestAgent/1.0',
                'Referer' => 'https://example.com',
            ])
            ->get('/test-analytics');

        $response->assertStatus(200);

        $event = AnalyticsEvent::first();
        expect($event)->not->toBeNull();
        expect($event->url)->toBe(url('/test-analytics'));
        expect($event->user_id)->toBe($user->id);
        expect($event->ip_address)->not->toBeNull();
        expect($event->user_agent)->toBe('TestAgent/1.0');
        expect($event->referrer)->toBe('https://example.com');
    });

    it('excludes admin routes from analytics capture', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin/test');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('analytics_events', [
            'url' => url('/admin/test'),
        ]);
    });

    it('captures null user_id for guest users', function () {
        $response = $this->get('/test-analytics');

        $response->assertStatus(200);

        $event = AnalyticsEvent::first();
        expect($event)->not->toBeNull();
        expect($event->user_id)->toBeNull();
    });

    it('captures null referrer when not provided', function () {
        $response = $this->get('/test-analytics');

        $response->assertStatus(200);

        $event = AnalyticsEvent::first();
        expect($event)->not->toBeNull();
        expect($event->referrer)->toBeNull();
    });

    it('creates separate analytics events for multiple requests', function () {
        foreach (range(1, 5) as $i) {
            $this->get('/test-analytics');
        }

        expect(AnalyticsEvent::count())->toBe(5);
    });
});
