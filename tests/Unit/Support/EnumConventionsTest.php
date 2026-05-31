<?php

use App\Support\Enums\AutomationRunStatus;
use App\Support\Enums\ChannelType;
use App\Support\Enums\InteractionDirection;
use App\Support\Enums\OutgoingActionStatus;
use App\Support\Enums\WebhookEventStatus;

it('uses lowercase snake or lowercase values for domain enums', function () {
    $values = [
        ...array_column(ChannelType::cases(), 'value'),
        ...array_column(InteractionDirection::cases(), 'value'),
        ...array_column(WebhookEventStatus::cases(), 'value'),
        ...array_column(AutomationRunStatus::cases(), 'value'),
        ...array_column(OutgoingActionStatus::cases(), 'value'),
    ];

    foreach ($values as $value) {
        expect($value)->toMatch('/^[a-z_]+$/');
    }
});

it('contains required outgoing action lifecycle states', function () {
    $statuses = array_column(OutgoingActionStatus::cases(), 'value');

    expect($statuses)->toContain('pending')
        ->toContain('in_progress')
        ->toContain('succeeded')
        ->toContain('failed')
        ->toContain('retry_pending');
});
