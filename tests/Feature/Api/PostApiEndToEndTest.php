<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('POST /api/v1/posts end-to-end', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
    });

    it('creates a post with a valid Sanctum token and create posts permission', function () {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $token = $user->createToken('post-api-test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/posts', [
                'title' => 'End-to-end API post',
                'post_type' => 'image',
                'caption' => 'End-to-end caption',
                'excerpt' => 'Created via API token.',
                'content' => 'This post was created through the API using a Sanctum token.',
                'status' => 'published',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'End-to-end API post');

        $this->assertDatabaseHas('posts', [
            'title' => 'End-to-end API post',
            'author_id' => $user->id,
        ]);
    });

    it('returns 403 when token lacks create posts permission', function () {
        $user = User::factory()->create();
        $user->assignRole('Viewer');

        $token = $user->createToken('post-api-denied')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/posts', [
                'title' => 'Unauthorized post',
                'post_type' => 'image',
                'caption' => 'Unauthorized caption',
                'status' => 'published',
            ]);

        $response->assertStatus(403);
    });
});
