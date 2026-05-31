<?php

namespace App\Support\DTOs;

final class NormalizedEventData
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $eventKey,
        public readonly string $eventType,
        public readonly string $channelKey,
        public readonly string $actorIdentityKey,
        public readonly ?string $threadKey,
        public readonly ?string $contentKey,
        public readonly ?string $body,
        public readonly string $occurredAt,
        public readonly array $payload = [],
        public readonly array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_key' => $this->eventKey,
            'event_type' => $this->eventType,
            'channel_key' => $this->channelKey,
            'actor_identity_key' => $this->actorIdentityKey,
            'thread_key' => $this->threadKey,
            'content_key' => $this->contentKey,
            'body' => $this->body,
            'occurred_at' => $this->occurredAt,
            'payload' => $this->payload,
            'metadata' => $this->metadata,
        ];
    }
}
