<?php

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\NewsletterPost;
use App\Models\Post;
use App\Models\User;
use App\Models\VideoPost;
use App\Policies\PostPolicy;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('PostPolicy', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->policy = new PostPolicy;
    });

    it('allows admin to perform any action', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $post = createPost();

        expect($this->policy->before($admin, 'view'))->toBeTrue();
        expect($this->policy->before($admin, 'create'))->toBeTrue();
        expect($this->policy->before($admin, 'delete'))->toBeTrue();
    });

    it('allows viewer to view posts', function () {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');
        $post = createPost();

        expect($this->policy->viewAny($viewer))->toBeTrue();
        expect($this->policy->view($viewer, $post))->toBeTrue();
    });

    it('denies viewer from creating posts', function () {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        expect($this->policy->create($viewer))->toBeFalse();
    });

    it('allows author to create posts', function () {
        $author = User::factory()->create();
        $author->assignRole('Author');

        expect($this->policy->create($author))->toBeTrue();
    });

    it('allows author to edit their own posts', function () {
        $author = User::factory()->create();
        $author->assignRole('Author');
        $post = createPost(['author_id' => $author->id]);

        expect($this->policy->update($author, $post))->toBeTrue();
    });

    it('denies author from editing others posts', function () {
        $author = User::factory()->create();
        $author->assignRole('Author');
        $otherUser = User::factory()->create();
        $post = createPost(['author_id' => $otherUser->id]);

        expect($this->policy->update($author, $post))->toBeFalse();
    });

    it('allows editor to edit any post', function () {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');
        $otherUser = User::factory()->create();
        $post = createPost(['author_id' => $otherUser->id]);

        expect($this->policy->update($editor, $post))->toBeTrue();
    });

    it('allows editor to publish posts', function () {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');
        $post = createPost();

        expect($this->policy->publish($editor, $post))->toBeTrue();
    });

    it('denies author from publishing posts', function () {
        $author = User::factory()->create();
        $author->assignRole('Author');
        $post = createPost(['author_id' => $author->id]);

        expect($this->policy->publish($author, $post))->toBeFalse();
    });
});

/**
 * Helper to create a post with polymorphic relationship.
 */
function createPost(array $attributes = []): Post
{
    $type = fake()->randomElement(PostType::cases());

    $postable = match ($type) {
        PostType::IMAGE => ImagePost::factory()->create(),
        PostType::VIDEO => VideoPost::factory()->create(),
        PostType::AUDIO => AudioPost::factory()->create(),
        PostType::NEWSLETTER => NewsletterPost::factory()->create(),
    };

    return Post::factory()->create(array_merge([
        'postable_type' => get_class($postable),
        'postable_id' => $postable->id,
        'status' => 'published',
    ], $attributes));
}
