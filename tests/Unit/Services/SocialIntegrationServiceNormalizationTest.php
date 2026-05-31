<?php

use App\Services\SocialIntegrationService;

it('normalizes fake provider payload to provider-neutral dto', function () {
    $service = new SocialIntegrationService;

    $normalized = $service->normalizeFakePayload([
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
        'metadata' => ['raw' => true],
    ], 'fake-channel-1');

    expect($normalized->eventKey)->toBe('evt_123')
        ->and($normalized->eventType)->toBe('message.received')
        ->and($normalized->channelKey)->toBe('fake-channel-1')
        ->and($normalized->actorIdentityKey)->toBe('fake:actor:42')
        ->and($normalized->threadKey)->toBe('th_001')
        ->and($normalized->contentKey)->toBe('ct_100')
        ->and($normalized->body)->toBe('hello bot')
        ->and($normalized->metadata['provider'])->toBe('fake')
        ->and($normalized->metadata['actor_display_name'])->toBe('Jane Doe');
});
