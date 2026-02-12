<?php

use App\Models\AnalyticsEvent;
use App\Services\PageViewService;

describe('Page View Service', function () {
    beforeEach(function () {
        $this->service = new PageViewService;
    });

    it('can count views for a specific URL', function () {
        $url = 'https://example.com/test-page';

        AnalyticsEvent::factory()->count(5)->forUrl($url)->create();
        AnalyticsEvent::factory()->count(3)->create(); // Different URLs

        $count = $this->service->countViews($url);

        expect($count)->toBe(5);
    });

    it('returns zero for URLs with no views', function () {
        $count = $this->service->countViews('https://example.com/non-existent');

        expect($count)->toBe(0);
    });

    it('can get top pages by view count', function () {
        // Create events with different URLs
        AnalyticsEvent::factory()->count(10)->forUrl('https://example.com/popular')->create();
        AnalyticsEvent::factory()->count(5)->forUrl('https://example.com/medium')->create();
        AnalyticsEvent::factory()->count(2)->forUrl('https://example.com/low')->create();

        $topPages = $this->service->topPages(2);

        expect($topPages)->toHaveCount(2);
        expect($topPages[0]->url)->toBe('https://example.com/popular');
        expect($topPages[0]->views)->toBe(10);
        expect($topPages[1]->url)->toBe('https://example.com/medium');
        expect($topPages[1]->views)->toBe(5);
    });

    it('returns all pages when limit exceeds available pages', function () {
        AnalyticsEvent::factory()->count(3)->forUrl('https://example.com/page1')->create();
        AnalyticsEvent::factory()->count(2)->forUrl('https://example.com/page2')->create();

        $topPages = $this->service->topPages(10);

        expect($topPages)->toHaveCount(2);
    });

    it('returns empty collection when no analytics exist', function () {
        $topPages = $this->service->topPages(10);

        expect($topPages)->toBeEmpty();
    });

    it('defaults to top 10 pages when no limit provided', function () {
        // Create 15 different URLs with 1 view each
        foreach (range(1, 15) as $i) {
            AnalyticsEvent::factory()->forUrl("https://example.com/page{$i}")->create();
        }

        $topPages = $this->service->topPages();

        expect($topPages)->toHaveCount(10);
    });

    it('orders top pages by view count descending', function () {
        AnalyticsEvent::factory()->count(3)->forUrl('https://example.com/page-c')->create();
        AnalyticsEvent::factory()->count(1)->forUrl('https://example.com/page-a')->create();
        AnalyticsEvent::factory()->count(5)->forUrl('https://example.com/page-b')->create();

        $topPages = $this->service->topPages(3);

        expect($topPages[0]->url)->toBe('https://example.com/page-b');
        expect($topPages[0]->views)->toBe(5);
        expect($topPages[1]->url)->toBe('https://example.com/page-c');
        expect($topPages[1]->views)->toBe(3);
        expect($topPages[2]->url)->toBe('https://example.com/page-a');
        expect($topPages[2]->views)->toBe(1);
    });
});
