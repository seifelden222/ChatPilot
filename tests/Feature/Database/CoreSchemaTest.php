<?php

use Illuminate\Support\Facades\Schema;

test('v1 provider-neutral core tables exist', function () {
    $tables = [
        'workspaces',
        'channels',
        'actors',
        'actor_identities',
        'content_items',
        'threads',
        'interactions',
        'automations',
        'automation_triggers',
        'automation_steps',
        'automation_runs',
        'automation_run_steps',
        'outgoing_actions',
        'webhook_events',
        'tags',
    ];

    foreach ($tables as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
    }
});

test('platform-specific tables are not introduced', function () {
    $blockedTables = [
        'instagram_comments',
        'facebook_posts',
        'whatsapp_messages',
        'telegram_messages',
    ];

    foreach ($blockedTables as $table) {
        expect(Schema::hasTable($table))->toBeFalse();
    }
});

test('core tables include searchable columns required by spec', function () {
    expect(Schema::hasColumns('channels', ['workspace_id', 'type', 'provider_key', 'external_id']))->toBeTrue();
    expect(Schema::hasColumns('actor_identities', ['workspace_id', 'channel_id', 'actor_id', 'identity_key']))->toBeTrue();
    expect(Schema::hasColumns('threads', ['workspace_id', 'channel_id', 'thread_key', 'status']))->toBeTrue();
    expect(Schema::hasColumns('interactions', ['workspace_id', 'channel_id', 'thread_id', 'actor_id', 'content_item_id', 'status', 'occurred_at']))->toBeTrue();
    expect(Schema::hasColumns('webhook_events', ['workspace_id', 'channel_id', 'event_key', 'event_type', 'status', 'received_at']))->toBeTrue();
    expect(Schema::hasColumns('outgoing_actions', ['workspace_id', 'channel_id', 'status', 'scheduled_at']))->toBeTrue();
});
