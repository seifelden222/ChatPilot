# Data Model: ChatPilot V1 Core System

## Entity Overview

V1 entities remain provider-neutral and map to constitution-approved vocabulary.

## Workspace

- Purpose: Multi-tenant boundary.
- Key fields:
  - id
  - name
  - slug
  - settings (json)
  - created_at, updated_at
- Relationships:
  - hasMany Channel
  - hasMany Automation
  - hasMany Tag

## Channel

- Purpose: Communication surface inside workspace.
- Key fields:
  - id
  - workspace_id (indexed)
  - name
  - type (enum, provider-neutral)
  - external_ref (indexed)
  - capabilities (json)
  - settings (json)
  - is_active
- Relationships:
  - belongsTo Workspace
  - hasMany ActorIdentity
  - hasMany Interaction
  - hasMany WebhookEvent

## Actor

- Purpose: Canonical participant.
- Key fields:
  - id
  - workspace_id (indexed)
  - display_name
  - actor_type (enum)
  - metadata (json)
- Relationships:
  - belongsTo Workspace
  - hasMany ActorIdentity
  - hasMany Interaction

## ActorIdentity

- Purpose: Channel-scoped identity mapping to actor.
- Key fields:
  - id
  - workspace_id (indexed)
  - channel_id (indexed)
  - actor_id (indexed)
  - identity_key (indexed)
  - identity_data (json)
- Constraints:
  - unique(workspace_id, channel_id, identity_key)
- Relationships:
  - belongsTo Workspace
  - belongsTo Channel
  - belongsTo Actor

## ContentItem

- Purpose: Provider-neutral content container.
- Key fields:
  - id
  - workspace_id (indexed)
  - channel_id (indexed)
  - content_key (indexed)
  - content_type (enum)
  - title
  - body
  - metadata (json)
- Relationships:
  - belongsTo Workspace
  - belongsTo Channel
  - hasMany Interaction

## Thread

- Purpose: Conversation grouping.
- Key fields:
  - id
  - workspace_id (indexed)
  - channel_id (indexed)
  - thread_key (indexed)
  - status (enum)
  - metadata (json)
  - last_interaction_at
- Constraints:
  - unique(workspace_id, channel_id, thread_key)
- Relationships:
  - belongsTo Workspace
  - belongsTo Channel
  - hasMany Interaction

## Interaction

- Purpose: Inbound/outbound conversation event record.
- Key fields:
  - id
  - workspace_id (indexed)
  - channel_id (indexed)
  - thread_id (indexed)
  - actor_id (indexed)
  - actor_identity_id (indexed)
  - content_item_id (nullable, indexed)
  - direction (enum: inbound/outbound/system)
  - interaction_key (indexed)
  - body
  - status (enum)
  - occurred_at (indexed)
  - metadata (json)
- Constraints:
  - unique(workspace_id, channel_id, interaction_key)
- Relationships:
  - belongsTo Workspace
  - belongsTo Channel
  - belongsTo Thread
  - belongsTo Actor
  - belongsTo ActorIdentity
  - belongsTo ContentItem
  - hasMany AutomationRun

## Automation

- Purpose: User-defined automation definition.
- Key fields:
  - id
  - workspace_id (indexed)
  - name
  - status (enum: active/paused/draft)
  - settings (json)
- Relationships:
  - belongsTo Workspace
  - hasMany AutomationTrigger
  - hasMany AutomationStep
  - hasMany AutomationRun

## AutomationTrigger

- Purpose: Match conditions.
- Key fields:
  - id
  - automation_id (indexed)
  - trigger_type (enum, V1 includes keyword)
  - conditions (json)
  - is_active
- Relationships:
  - belongsTo Automation

## AutomationStep

- Purpose: Action instructions.
- Key fields:
  - id
  - automation_id (indexed)
  - step_order
  - step_type (enum)
  - configuration (json)
  - is_active
- Constraints:
  - unique(automation_id, step_order)
- Relationships:
  - belongsTo Automation
  - hasMany AutomationRunStep

## AutomationRun

- Purpose: Runtime instance for matched interaction.
- Key fields:
  - id
  - workspace_id (indexed)
  - automation_id (indexed)
  - interaction_id (indexed)
  - status (enum)
  - started_at
  - completed_at
  - summary (json)
- Relationships:
  - belongsTo Workspace
  - belongsTo Automation
  - belongsTo Interaction
  - hasMany AutomationRunStep
  - hasMany OutgoingAction

## AutomationRunStep

- Purpose: Execution state per step in run.
- Key fields:
  - id
  - automation_run_id (indexed)
  - automation_step_id (indexed)
  - step_order
  - status (enum)
  - result (json)
  - executed_at
- Constraints:
  - unique(automation_run_id, step_order)
- Relationships:
  - belongsTo AutomationRun
  - belongsTo AutomationStep

## OutgoingAction

- Purpose: Pending/processed outbound action command.
- Key fields:
  - id
  - workspace_id (indexed)
  - channel_id (indexed)
  - automation_run_id (indexed)
  - interaction_id (indexed)
  - action_type (enum)
  - payload (json)
  - status (enum: pending/in_progress/succeeded/failed/retry_pending)
  - provider_response (json)
  - attempts
  - scheduled_at
  - executed_at
  - failed_at
  - failure_reason
- Relationships:
  - belongsTo Workspace
  - belongsTo Channel
  - belongsTo AutomationRun
  - belongsTo Interaction

## WebhookEvent

- Purpose: Durable ingress capture and processing lifecycle.
- Key fields:
  - id
  - workspace_id (indexed)
  - channel_id (indexed)
  - event_key (indexed)
  - event_type
  - payload (json)
  - headers (json)
  - status (enum: received/processing/processed/failed/ignored)
  - normalized_data (json)
  - received_at (indexed)
  - processed_at
  - error_message
- Constraints:
  - unique(workspace_id, channel_id, event_key)
- Relationships:
  - belongsTo Workspace
  - belongsTo Channel

## Tag

- Purpose: Classification labels.
- Key fields:
  - id
  - workspace_id (indexed)
  - name
  - slug
  - color
- Constraints:
  - unique(workspace_id, slug)
- Relationships:
  - belongsTo Workspace
  - morphToMany Interaction / Actor / Thread / ContentItem (as needed in V1)

## Validation Rules Snapshot

- Identity and event deduplication keys must be unique in workspace+channel scope.
- Search-critical keys (event_key, interaction_key, thread_key, identity_key, status fields) must be columns and indexed.
- JSON fields are limited to flexible provider data, payloads, and settings.

## State Transitions

- WebhookEvent: received -> processing -> processed | failed | ignored
- AutomationRun: pending -> running -> completed | failed
- AutomationRunStep: pending -> running -> completed | failed | skipped
- OutgoingAction: pending -> in_progress -> succeeded | failed | retry_pending
- Interaction (optional lifecycle): new -> processed | archived
