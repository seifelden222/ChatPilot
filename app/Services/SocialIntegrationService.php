<?php

namespace App\Services;

use App\Models\WebhookEvent;
use App\Support\DTOs\NormalizedEventData;
use Carbon\CarbonImmutable;

class SocialIntegrationService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function normalizeFakePayload(array $payload, string $channelKey): NormalizedEventData
    {
        $occurredAt = isset($payload['occurred_at'])
            ? (string) $payload['occurred_at']
            : CarbonImmutable::now()->toIso8601String();

        return new NormalizedEventData(
            eventKey: (string) ($payload['event_id'] ?? ''),
            eventType: (string) ($payload['event_type'] ?? 'message.received'),
            channelKey: $channelKey,
            actorIdentityKey: (string) data_get($payload, 'actor.identity', ''),
            threadKey: data_get($payload, 'thread.id') ? (string) data_get($payload, 'thread.id') : null,
            contentKey: data_get($payload, 'content.id') ? (string) data_get($payload, 'content.id') : null,
            body: data_get($payload, 'content.text') ? (string) data_get($payload, 'content.text') : null,
            occurredAt: $occurredAt,
            payload: $payload,
            metadata: [
                'provider' => 'fake',
                'actor_display_name' => data_get($payload, 'actor.display_name'),
                'thread' => data_get($payload, 'thread', []),
                'content' => data_get($payload, 'content', []),
                'metadata' => data_get($payload, 'metadata', []),
            ],
        );
    }

    public function normalizeWebhookEvent(WebhookEvent $webhookEvent): NormalizedEventData
    {
        return $this->normalizeFakePayload(
            payload: $webhookEvent->payload,
            channelKey: (string) ($webhookEvent->channel->external_id ?? $webhookEvent->channel_id),
        );
    }
}
