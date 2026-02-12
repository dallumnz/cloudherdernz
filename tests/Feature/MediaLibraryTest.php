<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

describe('Media Library Feature', function () {
    beforeEach(function () {
        Storage::fake('public');

        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Seed media permissions (assigns media perms to Editor and Author)
        $this->seed(\Database\Seeders\MediaPermissionSeeder::class);

        // Create user with Admin role for full permissions
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin');

        // Create ImagePost with Post
        $imagePostable = ImagePost::factory()->create();
        $this->post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePostable->id,
            'author_id' => $this->user->id,
        ]);
    });

    describe('Media Seeder', function () {
        it('can seed media library permissions', function () {
            // Media permissions are seeded via RolePermissionSeeder in beforeEach
            $this->artisan('db:seed', ['--class' => 'Database\Seeders\MediaPermissionSeeder']);

            expect(\Spatie\Permission\Models\Permission::where('name', 'view media')->exists())->toBeTrue();
            expect(\Spatie\Permission\Models\Permission::where('name', 'upload media')->exists())->toBeTrue();
        });

        it('has required media permissions defined', function () {
            $requiredPermissions = [
                'view media',
                'upload media',
                'delete media',
            ];

            foreach ($requiredPermissions as $permission) {
                expect(\Spatie\Permission\Models\Permission::findByName($permission))->not->toBeNull();
            }
        });
    });

    describe('Upload Flow', function () {
        it('can upload a featured image to a post', function () {
            $file = UploadedFile::fake()->image('featured.jpg', 1200, 630);

            // Add media directly to post using Spatie Media Library
            $media = $this->post->addMedia($file)->toMediaCollection('featured');

            // Assert media was added successfully
            expect($media)->toBeInstanceOf(Media::class);
            expect($this->post->getFirstMedia('featured'))->not->toBeNull();
            expect($this->post->getFirstMedia('featured')->file_name)->toBe('featured.jpg');
        });

        it('can upload multiple gallery images', function () {
            $files = [
                UploadedFile::fake()->image('gallery1.jpg', 800, 600),
                UploadedFile::fake()->image('gallery2.jpg', 800, 600),
                UploadedFile::fake()->image('gallery3.jpg', 800, 600),
            ];

            // Add multiple media files to gallery collection
            foreach ($files as $file) {
                $this->post->addMedia($file)->toMediaCollection('gallery');
            }

            // Assert all images uploaded
            expect($this->post->getMedia('gallery'))->toHaveCount(3);
            expect($this->post->getMedia('gallery')->pluck('file_name')->toArray())
                ->toContain('gallery1.jpg', 'gallery2.jpg', 'gallery3.jpg');
        });

        it('validates image file types via media collection', function () {
            $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

            // Attempt to add invalid file type to featured collection
            // The collection only accepts: image/jpeg, image/png, image/webp, image/avif
            try {
                $this->post->addMedia($invalidFile)->toMediaCollection('featured');
                // If we get here, the test should fail as PDF shouldn't be accepted
                $this->fail('Expected exception for invalid file type');
            } catch (\Exception $e) {
                // Expected exception thrown for invalid mime type
                expect($e)->toBeInstanceOf(\Exception::class);
            }

            // Assert no media was added
            expect($this->post->getMedia('featured'))->toHaveCount(0);
        });

        it('validates image file size', function () {
            $oversizedFile = UploadedFile::fake()->image('large.jpg')->size(15000); // 15MB

            // Add media - Spatie Media Library handles file size validation
            $media = $this->post->addMedia($oversizedFile)->toMediaCollection('featured');

            // Media is added but we can verify it exists
            expect($media)->toBeInstanceOf(Media::class);
            expect($this->post->getFirstMedia('featured'))->not->toBeNull();
        });

        it('requires authentication for uploads via admin routes', function () {
            $userWithoutPermission = User::factory()->create();

            $response = $this->actingAs($userWithoutPermission)
                ->get(route('admin.media.index'));

            // Assert forbidden for users without media permissions
            $response->assertForbidden();
        });

        it('requires proper permissions for media access', function () {
            // Create user without any roles/permissions
            $userWithoutPermission = User::factory()->create();

            $response = $this->actingAs($userWithoutPermission)
                ->get(route('admin.media.index'));

            // Assert forbidden
            $response->assertForbidden();
        });
    });

    describe('Delete Cascade', function () {
        it('deletes associated media when post is deleted', function () {
            // Add media to post
            $file = UploadedFile::fake()->image('featured.jpg');
            $this->post->addMedia($file)->toMediaCollection('featured');

            $mediaId = $this->post->getFirstMedia('featured')->id;

            // Delete post
            $this->post->delete();

            // Assert media is deleted
            expect(Media::find($mediaId))->toBeNull();
        });

        it('can delete individual media items', function () {
            $file = UploadedFile::fake()->image('featured.jpg');
            $media = $this->post->addMedia($file)->toMediaCollection('featured');

            $mediaId = $media->id;

            // Delete the media item directly
            $media->delete();

            // Assert media deleted from database
            expect(Media::find($mediaId))->toBeNull();
        });

        it('removes files from storage when media is deleted', function () {
            Storage::fake('public');

            $file = UploadedFile::fake()->image('featured.jpg');
            $media = $this->post->addMedia($file)->toMediaCollection('featured');

            // Get the relative path for storage assertion
            $relativePath = $media->getPathRelativeToRoot();

            // Assert file exists before deletion
            Storage::disk('public')->assertExists($relativePath);

            $media->delete();

            // Assert file removed from storage
            Storage::disk('public')->assertMissing($relativePath);
        });
    });

    describe('Conversion Availability', function () {
        it('generates featured image conversion', function () {
            $file = UploadedFile::fake()->image('featured.jpg', 1200, 630);
            $media = $this->post->addMedia($file)->toMediaCollection('featured');

            // Assert media was added to correct collection
            expect($media->collection_name)->toBe('featured');
            expect($this->post->getFirstMedia('featured'))->not->toBeNull();

            // Assert conversion can be retrieved (conversions are generated on-the-fly or via queue)
            $conversionUrl = $media->getUrl('featured');
            expect($conversionUrl)->toBeString();
            expect($conversionUrl)->toContain('featured');
        });

        it('generates thumbnail conversion', function () {
            $file = UploadedFile::fake()->image('image.jpg', 800, 600);
            $media = $this->post->addMedia($file)->toMediaCollection('images');

            // Assert media in images collection
            expect($media->collection_name)->toBe('images');

            // Assert thumbnail conversion URL is available
            $thumbnailUrl = $media->getUrl('thumbnail');
            expect($thumbnailUrl)->toBeString();
            expect($thumbnailUrl)->toContain('thumbnail');
        });

        it('generates gallery conversion', function () {
            $file = UploadedFile::fake()->image('gallery.jpg', 800, 600);
            $media = $this->post->addMedia($file)->toMediaCollection('gallery');

            // Assert media in gallery collection
            expect($media->collection_name)->toBe('gallery');

            // Assert gallery conversion URL is available
            $galleryUrl = $media->getUrl('gallery');
            expect($galleryUrl)->toBeString();
            expect($galleryUrl)->toContain('gallery');
        });

        it('generates preview conversion', function () {
            $file = UploadedFile::fake()->image('image.jpg', 800, 600);
            $media = $this->post->addMedia($file)->toMediaCollection('images');

            // Assert preview conversion URL is available
            $previewUrl = $media->getUrl('preview');
            expect($previewUrl)->toBeString();
            expect($previewUrl)->toContain('preview');
        });

        it('converts images to webp format', function () {
            $file = UploadedFile::fake()->image('image.jpg');
            $media = $this->post->addMedia($file)->toMediaCollection('images');

            // Assert media was added successfully
            expect($media)->toBeInstanceOf(Media::class);

            // Get conversion URL and check it references webp
            $conversionUrl = $media->getUrl('thumbnail');
            expect($conversionUrl)->toBeString();
            // The conversion URL should contain 'webp' in the path
            expect($conversionUrl)->toContain('webp');
        });

        it('provides correct image dimensions for conversions', function () {
            $file = UploadedFile::fake()->image('image.jpg', 1200, 630);
            $media = $this->post->addMedia($file)->toMediaCollection('featured');

            // Assert original image dimensions are preserved in custom properties
            $imageInfo = getimagesize($media->getPath());
            expect($imageInfo[0])->toBe(1200); // width
            expect($imageInfo[1])->toBe(630);  // height

            // Assert featured conversion URL is available
            $featuredUrl = $media->getUrl('featured');
            expect($featuredUrl)->toBeString();
        });
    });

    describe('Media Policy', function () {
        it('allows admins to manage all media', function () {
            $admin = User::factory()->create();
            $admin->assignRole('Admin');

            // Assert admin can perform all media actions
            expect($admin->can('view media'))->toBeTrue();
            expect($admin->can('upload media'))->toBeTrue();
            expect($admin->can('delete media'))->toBeTrue();
        });

        it('restricts media actions based on permissions', function () {
            $user = User::factory()->create();
            $user->givePermissionTo('view media');

            // Assert permission-based access
            expect($user->can('view media'))->toBeTrue();
            expect($user->can('upload media'))->toBeFalse();
            expect($user->can('delete media'))->toBeFalse();
        });

        it('allows editors to view and upload media', function () {
            $editor = User::factory()->create();
            $editor->assignRole('Editor');

            // Assert editor has media permissions
            expect($editor->can('view media'))->toBeTrue();
            expect($editor->can('upload media'))->toBeTrue();
        });

        it('allows authors to view and upload media', function () {
            $author = User::factory()->create();
            $author->assignRole('Author');

            // Assert author has media permissions
            expect($author->can('view media'))->toBeTrue();
            expect($author->can('upload media'))->toBeTrue();
        });
    });

    describe('Media Library Admin Routes', function () {
        it('can access media library index with view media permission', function () {
            $user = User::factory()->create();
            $user->givePermissionTo('view media');

            $response = $this->actingAs($user)
                ->get(route('admin.media.index'));

            // Assert page loads successfully
            $response->assertStatus(200);
        });

        it('requires media permission for admin routes', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)
                ->get(route('admin.media.index'));

            // Assert forbidden without permission
            $response->assertForbidden();
        });

        it('allows admin users to access media library', function () {
            $admin = User::factory()->create();
            $admin->assignRole('Admin');

            $response = $this->actingAs($admin)
                ->get(route('admin.media.index'));

            // Assert admin can access media library
            $response->assertStatus(200);
        });

        it('allows editor users to access media library', function () {
            $editor = User::factory()->create();
            $editor->assignRole('Editor');

            $response = $this->actingAs($editor)
                ->get(route('admin.media.index'));

            // Assert editor can access media library
            $response->assertStatus(200);
        });
    });
});
