<?php

use App\Jobs\ProcessWebhookEventJob;
use App\Models\Channel;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Support\Facades\Queue;

it('does not duplicate webhook event records or queue jobs for same event id in a channel', function () {
    Queue::fake();

    $workspace = Workspace::query()->create([
        'name' => 'Acme',
        'slug' => 'acme',
    ]);

    $channel = Channel::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Fake Channel',
        'type' => 'messaging',
        'provider_key' => 'fake',
        'external_id' => 'fake-channel-1',
    ]);

    $payload = [
        'event_id' => 'evt_123',
        'event_type' => 'message.received',
        'occurred_at' => '2026-05-31T12:00:00Z',
        'actor' => ['identity' => 'fake:actor:42'],
    ];

    $firstResponse = $this->postJson('/api/webhooks/fake/'.$channel->external_id, $payload);
    $secondResponse = $this->postJson('/api/webhooks/fake/'.$channel->external_id, $payload);

    $firstResponse->assertAccepted()->assertJsonPath('queued', true);
    $secondResponse->assertAccepted()->assertJsonPath('queued', false);

    expect(WebhookEvent::query()->count())->toBe(1);

    Queue::assertPushed(ProcessWebhookEventJob::class, 1);
});
