<?php

use App\Jobs\ProcessWebhookEventJob;
use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Support\Facades\Queue;

it('accepts valid webhook payload, persists event, and queues processing', function () {
    Queue::fake();

    $workspace = Workspace::factory()->create();

    $channel = Channel::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Fake Channel',
        'type' => 'messaging',
        'provider_key' => 'fake',
        'external_id' => 'fake-channel-1',
    ]);

    $response = $this->postJson('/api/webhooks/fake/'.$channel->external_id, [
        'event_id' => 'evt_123',
        'event_type' => 'message.received',
        'occurred_at' => '2026-05-31T12:00:00Z',
        'actor' => [
            'identity' => 'fake:actor:42',
            'display_name' => 'Jane Doe',
        ],
        'thread' => ['id' => 'th_001'],
        'content' => [
            'id' => 'ct_100',
            'text' => 'hello bot',
        ],
    ]);

    $response->assertAccepted()
        ->assertJson([
            'status' => 'accepted',
            'queued' => true,
        ]);

    expect($workspace->channels()->first()->webhookEvents()->count())->toBe(1);

    Queue::assertPushed(ProcessWebhookEventJob::class, 1);
});

it('returns validation errors for invalid payload', function () {
    $response = $this->postJson('/api/webhooks/fake/any-channel', []);

    $response->assertUnprocessable()
        ->assertJsonPath('status', 'rejected');
});

it('returns not found when channel does not exist', function () {
    $response = $this->postJson('/api/webhooks/fake/missing-channel', [
        'event_id' => 'evt_404',
        'event_type' => 'message.received',
        'occurred_at' => '2026-05-31T12:00:00Z',
        'actor' => ['identity' => 'fake:actor:missing'],
    ]);

    $response->assertNotFound()
        ->assertJson([
            'status' => 'rejected',
            'message' => 'Channel not found.',
        ]);
});
