<?php

use App\Events\OutgoingActionCreated;
use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\AutomationRunStep;
use App\Models\AutomationStep;
use App\Models\Channel;
use App\Models\Interaction;
use App\Models\OutgoingAction;
use App\Models\Thread;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AutomationExecutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

function makeAutomationForExecution(Workspace $workspace): Automation
{
    return Automation::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Execution automation',
        'status' => 'active',
    ]);
}

function makeExecutionWorkspace(): Workspace
{
    $user = User::factory()->create();

    return Workspace::unguarded(function () use ($user): Workspace {
        return Workspace::query()->create([
            'user_id' => $user->id,
            'name' => 'Acme',
            'slug' => 'acme-'.fake()->unique()->randomNumber(4),
        ]);
    });
}

function makeInteractionForExecution(Workspace $workspace): Interaction
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
        'body' => 'hello',
        'occurred_at' => now(),
        'metadata' => [],
    ]);
}

it('creates an automation run and run steps for matched automations', function () {
    $workspace = makeExecutionWorkspace();

    $automation = makeAutomationForExecution($workspace);
    AutomationStep::query()->create([
        'automation_id' => $automation->id,
        'step_order' => 2,
        'step_type' => 'log',
        'configuration' => ['note' => 'later'],
        'is_active' => true,
    ]);
    AutomationStep::query()->create([
        'automation_id' => $automation->id,
        'step_order' => 1,
        'step_type' => 'log',
        'configuration' => ['note' => 'first'],
        'is_active' => true,
    ]);

    $interaction = makeInteractionForExecution($workspace);

    $run = app(AutomationExecutor::class)->execute($automation, $interaction);

    expect(AutomationRun::query()->count())->toBe(1)
        ->and($run->status)->toBe('completed')
        ->and(AutomationRunStep::query()->orderBy('step_order')->pluck('step_order')->all())->toBe([1, 2]);
});

it('creates pending outgoing actions for send message and send reply steps', function () {
    Event::fake([OutgoingActionCreated::class]);

    $workspace = makeExecutionWorkspace();

    $automation = makeAutomationForExecution($workspace);
    AutomationStep::query()->create([
        'automation_id' => $automation->id,
        'step_order' => 1,
        'step_type' => 'send_message',
        'configuration' => ['body' => 'Thanks for reaching out'],
        'is_active' => true,
    ]);
    AutomationStep::query()->create([
        'automation_id' => $automation->id,
        'step_order' => 2,
        'step_type' => 'send_reply',
        'configuration' => ['body' => 'Please check this'],
        'is_active' => true,
    ]);

    $interaction = makeInteractionForExecution($workspace);

    app(AutomationExecutor::class)->execute($automation, $interaction);

    expect(OutgoingAction::query()->count())->toBe(2)
        ->and(OutgoingAction::query()->pluck('status')->all())->toBe(['pending', 'pending']);

    Event::assertDispatched(OutgoingActionCreated::class, 2);
});
