<?php

namespace App\Services;

use App\Models\OutgoingAction;
use App\Models\WebhookEvent;
use App\Support\DTOs\NormalizedEventData;
use Carbon\CarbonImmutable;
use RuntimeException;

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

    /**
     * @return array<string, mixed>
     */
    public function executeOutgoingAction(OutgoingAction $outgoingAction): array
    {
        if (data_get($outgoingAction->payload, 'force_fail') === true) {
            throw new RuntimeException('Fake provider execution failed.');
        }

        if (! in_array($outgoingAction->action_type, ['send_message', 'send_reply'], true)) {
            throw new RuntimeException('Unsupported fake provider action type.');
        }

        return [
            'provider' => 'fake',
            'status' => 'sent',
            'external_message_id' => 'fake_msg_'.$outgoingAction->id,
        ];
    }
}
