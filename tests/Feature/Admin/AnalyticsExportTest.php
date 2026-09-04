<?php

use Database\Seeders\RolePermissionSeeder;

describe('Analytics Export', function (): void {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
    });

    it('requires view analytics permission', function (): void {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.analytics.export'));

        $response->assertForbidden();
    });

    it('exports analytics as csv with date range', function (): void {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)
            ->get(route('admin.analytics.export', [
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-05',
            ]));

        $response->assertOk();
        expect($response->headers->get('Content-Type'))->toContain('text/csv');
        expect($response->headers->get('Content-Disposition'))->toContain('analytics_');
    });

    it('exports analytics as csv with default date range', function (): void {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)
            ->get(route('admin.analytics.export'));

        $response->assertOk();
        expect($response->headers->get('Content-Type'))->toContain('text/csv');
    });
});
