<?php

use App\Livewire\SearchPosts;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

describe('SearchPosts Livewire Component', function () {
    it('renders successfully', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ], 200),
        ]);

        Livewire::test(SearchPosts::class)
            ->assertStatus(200);
    });

    it('has initial empty query state', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ], 200),
        ]);

        $component = Livewire::test(SearchPosts::class);

        expect($component->get('query'))->toBe('');
        expect($component->get('perPage'))->toBe(12);
    });

    it('fetches posts on mount', function () {
        $postData = [
            [
                'id' => 1,
                'title' => 'Test Post',
                'slug' => 'test-post',
                'excerpt' => 'Test excerpt',
                'featured_image' => null,
                'author' => ['name' => 'Test Author'],
                'type' => ['name' => 'Image Post'],
                'published_at' => now()->toIso8601String(),
                'taxonomy_terms' => [],
            ],
        ];

        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => $postData,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 1,
                ],
            ], 200),
        ]);

        $component = Livewire::test(SearchPosts::class);

        expect($component->get('results'))->toHaveCount(1);
        expect($component->get('results')[0]['title'])->toBe('Test Post');
    });

    it('searches posts when query is updated', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ], 200),
        ]);

        Livewire::test(SearchPosts::class)
            ->set('query', 'search term')
            ->assertSet('query', 'search term');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'search') && str_contains($request->url(), 'term');
        });
    });

    it('resets page when query changes', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 5,
                    'total' => 50,
                ],
            ], 200),
        ]);

        $component = Livewire::test(SearchPosts::class)
            ->set('page', 3)
            ->set('query', 'new search');

        expect($component->get('page'))->toBe(1);
    });

    it('handles api errors gracefully', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response(['message' => 'Server error'], 500),
        ]);

        $component = Livewire::test(SearchPosts::class);

        expect($component->get('errorMessage'))->not->toBeNull();
        expect($component->get('results'))->toBe([]);
    });

    it('handles network errors gracefully', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::throw(function () {
                throw new \Exception('Network error');
            }),
        ]);

        $component = Livewire::test(SearchPosts::class);

        expect($component->get('errorMessage'))->not->toBeNull();
    });

    it('paginates through results', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 3,
                    'total' => 30,
                ],
            ], 200),
        ]);

        $component = Livewire::test(SearchPosts::class);

        expect($component->instance()->hasMorePages())->toBeTrue();
        expect($component->instance()->hasPreviousPages())->toBeFalse();

        $component->call('nextPage');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'page=2');
        });
    });

    it('can navigate to specific page', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 5,
                    'total' => 50,
                ],
            ], 200),
        ]);

        Livewire::test(SearchPosts::class)
            ->call('goToPage', 3);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'page=3');
        });
    });

    it('respects per page setting', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ], 200),
        ]);

        Livewire::test(SearchPosts::class)
            ->set('perPage', 24);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'per_page=24');
        });
    });

    it('displays loading state while fetching', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ], 200),
        ]);

        $component = Livewire::test(SearchPosts::class);

        expect($component->get('isLoading'))->toBeFalse();
    });

    it('renders with accessibility attributes', function () {
        Http::fake([
            '*/api/v1/posts*' => Http::response([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ], 200),
        ]);

        Livewire::test(SearchPosts::class)
            ->assertSeeHtml('role="region"')
            ->assertSeeHtml('aria-label="Post search"')
            ->assertSeeHtml('aria-label="Search posts"');
    });
});
