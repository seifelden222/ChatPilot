<?php

use App\Events\InteractionCreated;
use App\Jobs\ProcessWebhookEventJob;
use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Channel;
use App\Models\Interaction;
use App\Models\Thread;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use App\Services\InteractionService;
use App\Services\SocialIntegrationService;
use Illuminate\Support\Facades\Event;

it('processes queued webhook into normalized interaction records', function () {
    Event::fake([InteractionCreated::class]);

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

    $webhookEvent = WebhookEvent::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'event_key' => 'evt_pipeline_001',
        'event_type' => 'message.received',
        'payload' => [
            'event_id' => 'evt_pipeline_001',
            'event_type' => 'message.received',
            'occurred_at' => '2026-05-31T12:00:00Z',
            'actor' => [
                'identity' => 'fake:actor:42',
                'display_name' => 'Jane Doe',
            ],
            'thread' => ['id' => 'th_001'],
            'content' => ['id' => 'ct_100', 'text' => 'hello bot'],
        ],
        'status' => 'received',
        'received_at' => now(),
    ]);

    $job = new ProcessWebhookEventJob($webhookEvent->id);
    $job->handle(
        app(SocialIntegrationService::class),
        app(InteractionService::class),
    );

    expect(Actor::query()->count())->toBe(1)
        ->and(ActorIdentity::query()->count())->toBe(1)
        ->and(Thread::query()->count())->toBe(1)
        ->and(Interaction::query()->count())->toBe(1);

    $webhookEvent->refresh();
    expect($webhookEvent->status)->toBe('processed')
        ->and($webhookEvent->normalized_data)->toBeArray();

    Event::assertDispatched(InteractionCreated::class, function (InteractionCreated $event): bool {
        return $event->interactionId > 0
            && $event->workspaceId > 0
            && $event->channelId > 0;
    });
});
