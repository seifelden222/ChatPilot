<?php

namespace App\Services;

use App\Actions\UpsertActorAction;
use App\Actions\UpsertActorIdentityAction;
use App\Events\InteractionCreated;
use App\Models\ContentItem;
use App\Models\Interaction;
use App\Models\Thread;
use App\Models\WebhookEvent;
use App\Support\DTOs\NormalizedEventData;
use App\Support\Enums\InteractionDirection;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class InteractionService
{
    public function __construct(
        public UpsertActorAction $upsertActorAction,
        public UpsertActorIdentityAction $upsertActorIdentityAction,
    ) {}

    public function ingestNormalizedEvent(
        WebhookEvent $webhookEvent,
        NormalizedEventData $eventData,
    ): Interaction {
        return DB::transaction(function () use ($webhookEvent, $eventData): Interaction {
            $thread = Thread::query()->updateOrCreate(
                [
                    'workspace_id' => $webhookEvent->workspace_id,
                    'channel_id' => $webhookEvent->channel_id,
                    'thread_key' => $eventData->threadKey ?? $eventData->eventKey,
                ],
                [
                    'status' => 'open',
                    'last_interaction_at' => CarbonImmutable::parse($eventData->occurredAt),
                    'metadata' => ['event_type' => $eventData->eventType],
                ],
            );

            $existingIdentity = $webhookEvent->channel->actorIdentities()
                ->where('workspace_id', $webhookEvent->workspace_id)
                ->where('identity_key', $eventData->actorIdentityKey)
                ->first();

            $actor = $existingIdentity?->actor
                ?? $this->upsertActorAction->handle(
                    workspaceId: $webhookEvent->workspace_id,
                    displayName: (string) ($eventData->metadata['actor_display_name'] ?? 'Unknown Actor'),
                    metadata: ['source' => 'webhook', 'event_key' => $eventData->eventKey],
                );

            $identity = $this->upsertActorIdentityAction->handle(
                workspaceId: $webhookEvent->workspace_id,
                channelId: $webhookEvent->channel_id,
                actorId: $actor->id,
                identityKey: $eventData->actorIdentityKey,
                identityData: ['channel_key' => $eventData->channelKey],
            );

            $contentItem = null;

            if ($eventData->contentKey !== null) {
                $contentItem = ContentItem::query()->updateOrCreate(
                    [
                        'workspace_id' => $webhookEvent->workspace_id,
                        'channel_id' => $webhookEvent->channel_id,
                        'content_key' => $eventData->contentKey,
                    ],
                    [
                        'content_type' => 'message',
                        'body' => $eventData->body,
                        'metadata' => ['event_type' => $eventData->eventType],
                    ],
                );
            }

            $interaction = Interaction::query()->updateOrCreate(
                [
                    'workspace_id' => $webhookEvent->workspace_id,
                    'channel_id' => $webhookEvent->channel_id,
                    'interaction_key' => $eventData->eventKey,
                ],
                [
                    'thread_id' => $thread->id,
                    'actor_id' => $actor->id,
                    'actor_identity_id' => $identity->id,
                    'content_item_id' => $contentItem?->id,
                    'direction' => InteractionDirection::Inbound->value,
                    'type' => 'message',
                    'status' => 'received',
                    'body' => $eventData->body,
                    'occurred_at' => CarbonImmutable::parse($eventData->occurredAt),
                    'metadata' => $eventData->metadata,
                ],
            );

            $thread->update(['last_interaction_at' => $interaction->occurred_at]);

            if ($interaction->wasRecentlyCreated) {
                InteractionCreated::dispatch(
                    interactionId: $interaction->id,
                    workspaceId: $interaction->workspace_id,
                    channelId: $interaction->channel_id,
                    threadId: $interaction->thread_id,
                    actorId: $interaction->actor_id,
                    occurredAt: $interaction->occurred_at->toIso8601String(),
                );
            }

            return $interaction;
        });
    }
}
