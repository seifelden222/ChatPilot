<?php

use App\Models\Actor;
use App\Models\ActorIdentity;
use App\Models\Automation;
use App\Models\AutomationStep;
use App\Models\AutomationTrigger;
use App\Models\Channel;
use App\Models\Interaction;
use App\Models\OutgoingAction;
use App\Models\Thread;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('processes the full fake provider pipeline end to end', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);

    $channel = Channel::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Fake Channel',
        'type' => 'messaging',
        'provider_key' => 'fake',
        'external_id' => 'fake-channel-1',
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

    $thread = Thread::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'thread_key' => 'th_001',
        'status' => 'open',
        'last_interaction_at' => now(),
        'metadata' => [],
    ]);

    $identity = ActorIdentity::query()->create([
        'workspace_id' => $workspace->id,
        'channel_id' => $channel->id,
        'actor_id' => $actor->id,
        'identity_key' => 'fake:actor:42',
        'identity_data' => [],
    ]);

    $automation = Automation::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Price follow-up',
        'status' => 'active',
    ]);

    AutomationTrigger::query()->create([
        'automation_id' => $automation->id,
        'trigger_type' => 'keyword',
        'conditions' => ['keyword' => 'price'],
        'is_active' => true,
    ]);

    AutomationStep::query()->create([
        'automation_id' => $automation->id,
        'step_order' => 1,
        'step_type' => 'send_reply',
        'configuration' => ['body' => 'Our pricing starts at...'],
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/webhooks/fake/'.$channel->external_id, [
        'event_id' => 'evt_pipeline_001',
        'event_type' => 'message.received',
        'occurred_at' => '2026-05-31T12:00:00Z',
        'actor' => [
            'identity' => 'fake:actor:42',
            'display_name' => 'Jane Doe',
        ],
        'thread' => ['id' => 'th_001'],
        'content' => [
            'id' => 'ct_100',
            'text' => 'What is the price?',
        ],
    ]);

    $response->assertAccepted();

    $webhookEvent = WebhookEvent::query()->firstOrFail();
    expect($webhookEvent->status)->toBe('processed');

    $interaction = Interaction::query()->firstOrFail();
    $run = $interaction->automationRuns()->firstOrFail();
    $outgoingAction = OutgoingAction::query()->firstOrFail();

    expect($interaction->body)->toBe('What is the price?')
        ->and($run->status)->toBe('completed')
        ->and($outgoingAction->status)->toBe('succeeded')
        ->and($outgoingAction->provider_response)->toMatchArray([
            'provider' => 'fake',
            'status' => 'sent',
            'external_message_id' => 'fake_msg_'.$outgoingAction->id,
        ]);
});
