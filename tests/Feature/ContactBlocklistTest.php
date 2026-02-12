<?php

use App\Models\ContactBlocklist;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ContactBlocklist Unit', function () {
    describe('blocking emails', function () {
        it('can block an email address', function () {
            $block = ContactBlocklist::blockEmail('test@example.com', 'Test reason');

            expect($block)->toBeInstanceOf(ContactBlocklist::class);
            expect($block->type)->toBe('email');
            expect($block->value)->toBe('test@example.com');
            expect($block->reason)->toBe('Test reason');

            $this->assertDatabaseHas('contact_blocklist', [
                'type' => 'email',
                'value' => 'test@example.com',
            ]);
        });

        it('can block a domain', function () {
            $block = ContactBlocklist::blockDomain('spamdomain.com', 'Known spam domain');

            expect($block->type)->toBe('domain');
            expect($block->value)->toBe('spamdomain.com');

            $this->assertDatabaseHas('contact_blocklist', [
                'type' => 'domain',
                'value' => 'spamdomain.com',
            ]);
        });

        it('prevents duplicate blocks', function () {
            ContactBlocklist::blockEmail('duplicate@test.com', 'First');
            $second = ContactBlocklist::blockEmail('duplicate@test.com', 'Second');

            expect(ContactBlocklist::where('type', 'email')
                ->where('value', 'duplicate@test.com')
                ->count())->toBe(1);
        });
    });

    describe('checking blocked emails', function () {
        it('returns true for blocked email', function () {
            ContactBlocklist::blockEmail('blocked@test.com');

            expect(ContactBlocklist::isEmailBlocked('blocked@test.com'))->toBeTrue();
        });

        it('returns false for non-blocked email', function () {
            expect(ContactBlocklist::isEmailBlocked('clean@test.com'))->toBeFalse();
        });

        it('returns true for email from blocked domain', function () {
            ContactBlocklist::blockDomain('baddomain.com');

            expect(ContactBlocklist::isEmailBlocked('user@baddomain.com'))->toBeTrue();
        });

        it('returns false when domain is not blocked', function () {
            ContactBlocklist::blockDomain('baddomain.com');

            expect(ContactBlocklist::isEmailBlocked('user@gooddomain.com'))->toBeFalse();
        });
    });

    describe('unblocking', function () {
        it('can unblock an email', function () {
            ContactBlocklist::blockEmail('unblock@test.com');

            $result = ContactBlocklist::unblock('email', 'unblock@test.com');

            expect($result)->toBeTrue();
            expect(ContactBlocklist::isEmailBlocked('unblock@test.com'))->toBeFalse();
        });

        it('can unblock a domain', function () {
            ContactBlocklist::blockDomain('unblockdomain.com');

            $result = ContactBlocklist::unblock('domain', 'unblockdomain.com');

            expect($result)->toBeTrue();
            expect(ContactBlocklist::isEmailBlocked('user@unblockdomain.com'))->toBeFalse();
        });

        it('returns false when trying to unblock non-existent entry', function () {
            $result = ContactBlocklist::unblock('email', 'nonexistent@test.com');

            expect($result)->toBeFalse();
        });
    });

    describe('scopes', function () {
        it('scopes emails only', function () {
            ContactBlocklist::blockEmail('email1@test.com');
            ContactBlocklist::blockDomain('domain1.com');

            $emails = ContactBlocklist::emails()->get();

            expect($emails)->toHaveCount(1);
            expect($emails->first()->type)->toBe('email');
        });

        it('scopes domains only', function () {
            ContactBlocklist::blockEmail('email2@test.com');
            ContactBlocklist::blockDomain('domain2.com');

            $domains = ContactBlocklist::domains()->get();

            expect($domains)->toHaveCount(1);
            expect($domains->first()->type)->toBe('domain');
        });
    });

    describe('soft deletes', function () {
        it('soft deletes blocklist entries', function () {
            $block = ContactBlocklist::blockEmail('softdelete@test.com');

            $block->delete();

            $this->assertDatabaseHas('contact_blocklist', [
                'type' => 'email',
                'value' => 'softdelete@test.com',
                'deleted_at' => now(),
            ]);

            expect(ContactBlocklist::find($block->id))->toBeNull();
            expect(ContactBlocklist::withTrashed()->find($block->id))->not->toBeNull();
        });
    });
});
