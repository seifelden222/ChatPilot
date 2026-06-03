<?php

use App\Events\OutgoingActionCreated;
use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Automation;
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

it('dispatches outgoing action created with the expected identifiers', function () {
    Event::fake([OutgoingActionCreated::class]);

    $user = User::factory()->create();
    $workspace = Workspace::unguarded(function () use ($user): Workspace {
        return Workspace::query()->create([
            'user_id' => $user->id,
            'name' => 'Acme',
            'slug' => 'acme-'.fake()->unique()->randomNumber(4),
        ]);
    });

    $automation = Automation::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Reply automation',
        'status' => 'active',
    ]);

    AutomationStep::query()->create([
        'automation_id' => $automation->id,
        'step_order' => 1,
        'step_type' => 'send_message',
        'configuration' => ['body' => 'Hello there'],
        'is_active' => true,
    ]);

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

    $interaction = Interaction::query()->create([
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

    $run = app(AutomationExecutor::class)->execute($automation, $interaction);
    $outgoingAction = OutgoingAction::query()->firstOrFail();

    Event::assertDispatched(OutgoingActionCreated::class, function (OutgoingActionCreated $event) use ($outgoingAction, $run, $interaction, $workspace, $channel): bool {
        return $event->outgoingActionId === $outgoingAction->id
            && $event->workspaceId === $workspace->id
            && $event->channelId === $channel->id
            && $event->automationRunId === $run->id
            && $event->interactionId === $interaction->id;
    });
});
