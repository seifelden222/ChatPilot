<?php

use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Automation;
use App\Models\AutomationTrigger;
use App\Models\Channel;
use App\Models\Interaction;
use App\Models\Thread;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AutomationMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAutomationForMatching(Workspace $workspace, array $triggerConditions, array $automationAttributes = [], array $triggerAttributes = []): Automation
{
    $automation = Automation::query()->create(array_merge([
        'workspace_id' => $workspace->id,
        'name' => 'Keyword automation',
        'status' => 'active',
    ], $automationAttributes));

    AutomationTrigger::query()->create(array_merge([
        'automation_id' => $automation->id,
        'trigger_type' => 'keyword',
        'conditions' => $triggerConditions,
        'is_active' => true,
    ], $triggerAttributes));

    return $automation;
}

function makeMatchingWorkspace(): Workspace
{
    $user = User::factory()->create();

    return Workspace::factory()->create(['user_id' => $user->id]);
}

function makeInteractionForMatching(Workspace $workspace, string $body): Interaction
{
    $channel = Channel::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Inbox',
        'type' => 'messaging',
        'provider_key' => 'fake',
        'external_id' => 'channel-1',
        'capabilities' => [],
        'settings' => [],
        'is_active' => true,
    ]);

    $actor = Actor::query()->create([
        'workspace_id' => $workspace->id,
        'display_name' => 'Jane Doe',
        'actor_type' => 'person',
        'metadata' => [],
    ]);

    $identity = ActorIdentity::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'actor_id' => $actor->id,
        'identity_key' => 'fake:actor:1',
        'identity_data' => [],
    ]);

    $thread = Thread::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'thread_key' => 'thread-1',
        'status' => 'open',
        'last_interaction_at' => now(),
        'metadata' => [],
    ]);

    return Interaction::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'thread_id' => $thread->id,
        'actor_id' => $actor->id,
        'actor_identity_id' => $identity->id,
        'interaction_key' => 'interaction-1',
        'direction' => 'inbound',
        'type' => 'message',
        'status' => 'received',
        'body' => $body,
        'occurred_at' => now(),
        'metadata' => [],
    ]);
}

it('matches active keyword automations against the interaction body', function () {
    $workspace = makeMatchingWorkspace();

    $matched = makeAutomationForMatching($workspace, ['keyword' => 'price']);
    makeAutomationForMatching($workspace, ['keyword' => 'shipping'], ['name' => 'Other automation']);

    $interaction = makeInteractionForMatching($workspace, 'Can you share the PRICE please?');

    $matches = app(AutomationMatcher::class)->match($interaction);

    expect($matches)->toHaveCount(1)
        ->and($matches->first()->is($matched))->toBeTrue();
});

it('does not match inactive automations', function () {
    $workspace = makeMatchingWorkspace();

    makeAutomationForMatching($workspace, ['keyword' => 'price'], ['status' => 'draft']);
    $interaction = makeInteractionForMatching($workspace, 'What is the price?');

    expect(app(AutomationMatcher::class)->match($interaction))->toHaveCount(0);
});

it('does not match inactive triggers', function () {
    $workspace = makeMatchingWorkspace();

    makeAutomationForMatching($workspace, ['keyword' => 'price'], [], ['is_active' => false]);
    $interaction = makeInteractionForMatching($workspace, 'What is the price?');

    expect(app(AutomationMatcher::class)->match($interaction))->toHaveCount(0);
});

it('matches keywords case-insensitively', function () {
    $workspace = makeMatchingWorkspace();

    $automation = makeAutomationForMatching($workspace, ['keyword' => 'Price']);
    $interaction = makeInteractionForMatching($workspace, 'I need the price now.');

    $matches = app(AutomationMatcher::class)->match($interaction);

    expect($matches)->toHaveCount(1)
        ->and($matches->first()->is($automation))->toBeTrue();
});

it('matches when any keyword in a list appears', function () {
    $workspace = makeMatchingWorkspace();

    $automation = makeAutomationForMatching($workspace, ['keywords' => ['price', 'cost']]);
    $interaction = makeInteractionForMatching($workspace, 'What is the COST?');

    $matches = app(AutomationMatcher::class)->match($interaction);

    expect($matches)->toHaveCount(1)
        ->and($matches->first()->is($automation))->toBeTrue();
});
