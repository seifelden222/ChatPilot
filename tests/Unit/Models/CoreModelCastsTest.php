<?php

use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\AutomationRunStep;
use App\Models\AutomationStep;
use App\Models\Channel;
use App\Models\ContentItem;
use App\Models\Interaction;
use App\Models\OutgoingAction;
use App\Models\Thread;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Tests\TestCase;

uses(TestCase::class);

it('defines expected JSON and datetime casts across core models', function () {
    expect((new Workspace)->getCasts())->toHaveKey('settings', 'array')
        ->and((new Channel)->getCasts())->toHaveKey('capabilities', 'array')
        ->and((new Channel)->getCasts())->toHaveKey('settings', 'array')
        ->and((new Actor)->getCasts())->toHaveKey('metadata', 'array')
        ->and((new ActorIdentity)->getCasts())->toHaveKey('identity_data', 'array')
        ->and((new ContentItem)->getCasts())->toHaveKey('metadata', 'array')
        ->and((new Thread)->getCasts())->toHaveKey('last_interaction_at', 'datetime')
        ->and((new Interaction)->getCasts())->toHaveKey('occurred_at', 'datetime')
        ->and((new Automation)->getCasts())->toHaveKey('settings', 'array')
        ->and((new AutomationStep)->getCasts())->toHaveKey('configuration', 'array')
        ->and((new AutomationRun)->getCasts())->toHaveKey('summary', 'array')
        ->and((new AutomationRunStep)->getCasts())->toHaveKey('executed_at', 'datetime')
        ->and((new OutgoingAction)->getCasts())->toHaveKey('payload', 'array')
        ->and((new OutgoingAction)->getCasts())->toHaveKey('provider_response', 'array')
        ->and((new WebhookEvent)->getCasts())->toHaveKey('payload', 'array')
        ->and((new WebhookEvent)->getCasts())->toHaveKey('normalized_data', 'array');
});
