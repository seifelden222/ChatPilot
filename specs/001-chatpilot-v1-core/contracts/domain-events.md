# Contract: Core Domain Events

## InteractionCreated

- Emitted when: a new Interaction is recorded from normalized webhook data.
- Payload contract:
  - interaction_id (int)
  - workspace_id (int)
  - channel_id (int)
  - thread_id (int)
  - actor_id (int)
  - occurred_at (datetime)
- Consumers:
  - MatchAutomationForInteraction listener

## OutgoingActionCreated

- Emitted when: a pending OutgoingAction is created by automation runner.
- Payload contract:
  - outgoing_action_id (int)
  - workspace_id (int)
  - channel_id (int)
  - action_type (string/enum)
  - status (pending)
- Consumers:
  - Dispatch/queue execution job for outgoing action

## Processing Contract Notes

- Event handlers MUST be idempotent where duplicate dispatch is possible.
- Long-running side effects MUST execute through queued jobs.
- Provider-specific branching in event consumers is prohibited outside SocialIntegrationService.
