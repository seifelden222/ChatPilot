<?php

use App\Jobs\ExecuteOutgoingActionJob;
use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\Channel;
use App\Models\Interaction;
use App\Models\OutgoingAction;
use App\Models\Thread;
use App\Models\User;
use App\Models\Workspace;
use App\Services\SocialIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not execute terminal outgoing actions again', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);

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

    $thread = Thread::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'thread_key' => 'thread-1',
        'status' => 'open',
        'last_interaction_at' => now(),
        'metadata' => [],
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

    $automation = Automation::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Reply automation',
        'status' => 'active',
    ]);

    $automationRun = AutomationRun::query()->create([
        'workspace_id' => $workspace->id,
        'automation_id' => $automation->id,
        'interaction_id' => $interaction->id,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
        'summary' => [],
    ]);

    $outgoingAction = OutgoingAction::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'automation_run_id' => $automationRun->id,
        'interaction_id' => $interaction->id,
        'action_type' => 'send_message',
        'payload' => ['body' => 'Hello'],
        'status' => 'succeeded',
        'provider_response' => ['provider' => 'fake'],
    ]);

    (new ExecuteOutgoingActionJob($outgoingAction->id))->handle(app(SocialIntegrationService::class));

    $outgoingAction->refresh();

    expect($outgoingAction->status)->toBe('succeeded')
        ->and($outgoingAction->provider_response)->toMatchArray(['provider' => 'fake']);
});
