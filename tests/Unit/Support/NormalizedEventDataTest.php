<?php

use App\Support\DTOs\NormalizedEventData;

test('normalized event data can be converted to array', function () {
    $dto = new NormalizedEventData(
        eventKey: 'evt_001',
        eventType: 'message.received',
        channelKey: 'channel_001',
        actorIdentityKey: 'actor_123',
        threadKey: 'thread_123',
        contentKey: 'content_123',
        body: 'hello',
        occurredAt: '2026-05-31T00:00:00Z',
        payload: ['foo' => 'bar'],
        metadata: ['source' => 'fake'],
    );

    expect($dto->toArray())
        ->toMatchArray([
            'event_key' => 'evt_001',
            'event_type' => 'message.received',
            'channel_key' => 'channel_001',
            'actor_identity_key' => 'actor_123',
            'thread_key' => 'thread_123',
            'content_key' => 'content_123',
            'body' => 'hello',
            'occurred_at' => '2026-05-31T00:00:00Z',
            'payload' => ['foo' => 'bar'],
            'metadata' => ['source' => 'fake'],
        ]);
});

test('normalized event data supports nullable optional keys', function () {
    $dto = new NormalizedEventData(
        eventKey: 'evt_002',
        eventType: 'comment.created',
        channelKey: 'channel_002',
        actorIdentityKey: 'actor_999',
        threadKey: null,
        contentKey: null,
        body: null,
        occurredAt: '2026-05-31T00:00:00Z',
    );

    expect($dto->threadKey)->toBeNull()
        ->and($dto->contentKey)->toBeNull()
        ->and($dto->body)->toBeNull();
});
