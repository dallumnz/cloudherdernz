<?php

use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

describe('Page Model', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
    });

    it('can create a page', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'title' => 'Test Page',
            'slug' => 'test-page',
            'content' => 'This is test content.',
        ]);

        expect($page)->toBeInstanceOf(Page::class);
        expect($page->title)->toBe('Test Page');
        expect($page->slug)->toBe('test-page');
        expect($page->content)->toBe('This is test content.');
        expect($page->author_id)->toBe($this->user->id);
    });

    it('has a unique slug', function () {
        Page::factory()->create([
            'author_id' => $this->user->id,
            'slug' => 'unique-slug',
        ]);

        expect(fn () => Page::factory()->create([
            'author_id' => $this->user->id,
            'slug' => 'unique-slug',
        ]))->toThrow(\Illuminate\Database\QueryException::class);
    });

    it('belongs to an author', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
        ]);

        expect($page->author)->toBeInstanceOf(User::class);
        expect($page->author->id)->toBe($this->user->id);
    });

    it('can be published', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        expect($page->isPublished())->toBeFalse();
        expect($page->isDraft())->toBeTrue();

        $page->publish();

        expect($page->fresh()->isPublished())->toBeTrue();
        expect($page->fresh()->isDraft())->toBeFalse();
    });

    it('can be unpublished', function () {
        $page = Page::factory()->published()->create([
            'author_id' => $this->user->id,
        ]);

        expect($page->isPublished())->toBeTrue();

        $page->unpublish();

        expect($page->fresh()->isPublished())->toBeFalse();
        expect($page->fresh()->isDraft())->toBeTrue();
    });

    it('scope published returns only published pages', function () {
        $publishedPage = Page::factory()->published()->create([
            'author_id' => $this->user->id,
        ]);
        $draftPage = Page::factory()->draft()->create([
            'author_id' => $this->user->id,
        ]);

        $publishedPages = Page::published()->get();

        expect($publishedPages)->toHaveCount(1);
        expect($publishedPages->first()->id)->toBe($publishedPage->id);
    });

    it('scope draft returns only draft pages', function () {
        $publishedPage = Page::factory()->published()->create([
            'author_id' => $this->user->id,
        ]);
        $draftPage = Page::factory()->draft()->create([
            'author_id' => $this->user->id,
        ]);

        $draftPages = Page::draft()->get();

        expect($draftPages)->toHaveCount(1);
        expect($draftPages->first()->id)->toBe($draftPage->id);
    });

    it('scope bySlug finds page by slug', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'slug' => 'my-custom-slug',
        ]);

        $foundPage = Page::bySlug('my-custom-slug')->first();

        expect($foundPage)->not->toBeNull();
        expect($foundPage->id)->toBe($page->id);
    });

    it('returns seo title from meta_title when set', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'title' => 'Page Title',
            'meta_title' => 'SEO Title',
        ]);

        expect($page->getSeoTitle())->toBe('SEO Title');
    });

    it('returns seo title from title when meta_title is not set', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'title' => 'Page Title',
            'meta_title' => null,
        ]);

        expect($page->getSeoTitle())->toBe('Page Title');
    });

    it('returns seo description from meta_description when set', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'meta_description' => 'Custom meta description',
        ]);

        expect($page->getSeoDescription())->toBe('Custom meta description');
    });

    it('returns seo description from content when meta_description is not set', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'meta_description' => null,
            'content' => '<p>This is a long content paragraph that should be used as meta description when no custom meta description is provided.</p>',
        ]);

        expect($page->getSeoDescription())->toContain('This is a long content paragraph');
    });

    it('supports soft deletes', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
        ]);

        $page->delete();

        expect(Page::find($page->id))->toBeNull();
        expect(Page::withTrashed()->find($page->id))->not->toBeNull();
    });
});
