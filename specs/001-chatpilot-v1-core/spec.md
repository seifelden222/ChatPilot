# Feature Specification: ChatPilot V1 Core System

**Feature Branch**: `001-chatpilot-v1-core`

**Created**: 2026-05-31

**Status**: Draft

**Input**: User description: "Create the V1 specification for ChatPilot based on the existing constitution."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Ingest and Normalize Webhook Interactions (Priority: P1)

A workspace operator can send fake provider webhook events to the system and have them processed into a consistent, provider-neutral interaction timeline tied to the right actor, content item, and thread.

**Why this priority**: Without reliable ingestion and normalization, no downstream automation behavior can function.

**Independent Test**: Can be fully tested by submitting fake provider webhook payloads and verifying creation or updates of webhook events, normalized event data, actor identity, and interaction records.

**Acceptance Scenarios**:

1. **Given** a valid fake provider webhook payload for an existing workspace channel, **When** the webhook is received, **Then** the system stores the raw event, queues processing, normalizes the payload, and creates or updates actor, actor identity, content item, thread, and interaction records.
2. **Given** a duplicate webhook payload, **When** processing runs again, **Then** the system avoids duplicate interaction creation and preserves a consistent single interaction history.
3. **Given** a webhook payload missing optional fields, **When** it is normalized, **Then** the system stores available data and marks missing optional values without failing the full processing pipeline.

---

### User Story 2 - Trigger and Run Keyword Automations (Priority: P2)

A workspace operator can define keyword-based automation rules that are evaluated when interactions are created, resulting in automation runs and outgoing actions.

**Why this priority**: This delivers the first business value of the platform by turning incoming interactions into automated responses.

**Independent Test**: Can be fully tested by creating automations with keyword triggers, creating matching and non-matching interactions, and verifying only matching interactions create automation runs and outgoing actions.

**Acceptance Scenarios**:

1. **Given** an active automation with a keyword trigger for a channel, **When** an interaction containing that keyword is created, **Then** the system creates an automation run and one or more outgoing actions according to automation steps.
2. **Given** an interaction that does not match any active trigger, **When** automation matching executes, **Then** no automation run or outgoing action is created.
3. **Given** multiple matching automations, **When** matching executes, **Then** the system creates distinct automation runs while preserving deterministic processing order.

---

### User Story 3 - Execute Outgoing Actions Asynchronously (Priority: P3)

A workspace operator can rely on queued execution of outgoing actions through the fake provider, with clear status tracking for success, retry, or failure.

**Why this priority**: Reliable asynchronous execution closes the loop from incoming interaction to outbound automation behavior.

**Independent Test**: Can be fully tested by creating outgoing actions, dispatching execution jobs, and verifying status transitions and recorded provider responses for success and failure paths.

**Acceptance Scenarios**:

1. **Given** a pending outgoing action, **When** the outgoing action event is fired, **Then** execution is queued and processed asynchronously through the integration service.
2. **Given** successful fake provider execution, **When** the job completes, **Then** the outgoing action status is updated to success and related interaction or thread statuses are updated.
3. **Given** provider execution failure, **When** the job completes, **Then** the outgoing action status is updated to failed or retry-pending with failure details recorded.

---

### Edge Cases

- A webhook references an unknown channel or workspace.
- A webhook arrives out of chronological order relative to earlier events.
- Multiple webhooks for the same thread arrive at near-identical times.
- An automation is disabled after interaction creation but before outgoing action execution.
- Keyword matching is ambiguous because multiple triggers overlap on the same phrase.
- Outgoing action execution is attempted for a record already marked terminal.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST support provider-neutral workspace and channel ownership of all core records.
- **FR-002**: System MUST accept fake provider webhook requests for configured channels.
- **FR-003**: System MUST persist raw webhook payloads and metadata in webhook events before downstream processing.
- **FR-004**: System MUST process webhook events asynchronously.
- **FR-005**: System MUST normalize fake provider payloads into a provider-neutral normalized event data structure.
- **FR-006**: System MUST create or update actor records from normalized event data.
- **FR-007**: System MUST create or update actor identity records linked to actors and channels.
- **FR-008**: System MUST create or update content item, thread, and interaction records from normalized event data.
- **FR-009**: System MUST emit an interaction created domain event when a new interaction is recorded.
- **FR-010**: System MUST evaluate active automation triggers for each created interaction.
- **FR-011**: System MUST support keyword-based trigger conditions for V1 automation matching.
- **FR-012**: System MUST create automation run records for each matched automation.
- **FR-013**: System MUST create automation run step and outgoing action records for matched runs.
- **FR-014**: System MUST emit an outgoing action created domain event for each pending outgoing action.
- **FR-015**: System MUST execute outgoing actions asynchronously through a single social integration service.
- **FR-016**: System MUST update outgoing action statuses to represent pending, in-progress, succeeded, failed, or retry-pending outcomes.
- **FR-017**: System MUST record execution responses and failure details for outgoing actions.
- **FR-018**: System MUST prevent duplicate processing effects for duplicate webhook events.
- **FR-019**: System MUST keep searchable business fields in dedicated columns and use JSON only for flexible metadata/payload/settings/capabilities/conditions/attachments/provider-responses.
- **FR-020**: System MUST avoid platform-specific core tables and preserve provider-neutral domain naming.
- **FR-021**: System MUST keep controllers, dashboard resources, and interactive components as thin orchestration layers.
- **FR-022**: System MUST confine provider-specific conditionals to SocialIntegrationService only.
- **FR-023**: System MUST include automated Pest coverage for webhook ingestion, normalization, interaction creation, keyword matching, outgoing action creation, and queued execution.
- **FR-024**: System MUST exclude real provider API integrations, real billing flows, and visual flow builder capabilities from V1.

### Constitution Alignment *(mandatory)*

- **CA-001**: This feature preserves Laravel-first architecture with business logic in service-domain layers and thin orchestration layers.
- **CA-002**: This feature uses provider-neutral domain naming across data and workflow boundaries.
- **CA-003**: V1 provider-specific behavior is centralized in SocialIntegrationService only.
- **CA-004**: Webhook and outgoing action handling are queue-first and event-driven.
- **CA-005**: Searchable fields are stored in columns; JSON usage is limited to flexible, non-index-critical payload categories.
- **CA-006**: V1 excludes real provider APIs, visual flow builder, and real billing.
- **CA-007**: Pest coverage is required for every critical core behavior defined in this specification.

### Key Entities *(include if feature involves data)*

- **Workspace**: Tenant boundary for channels, actors, interactions, and automations.
- **Channel**: Provider-neutral communication surface linked to a workspace.
- **Actor**: Canonical person or organization participating in interactions.
- **ActorIdentity**: Channel-scoped identity for an actor.
- **ContentItem**: Provider-neutral representation of authored content linked to interactions.
- **Thread**: Conversation grouping that contains one or more interactions.
- **Interaction**: Inbound or stateful conversation event tied to actor, channel, and thread.
- **Automation**: User-defined automation definition within a workspace.
- **AutomationTrigger**: Condition definition that determines whether an automation should run.
- **AutomationStep**: Ordered action instruction inside an automation.
- **AutomationRun**: Execution instance of an automation for a specific interaction.
- **AutomationRunStep**: Execution state for each step in an automation run.
- **OutgoingAction**: Action request produced by automation for outbound execution.
- **WebhookEvent**: Raw inbound webhook record plus processing lifecycle status.
- **Tag**: Classification label attachable to relevant domain records.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of accepted fake provider webhooks are durably recorded before asynchronous processing begins.
- **SC-002**: At least 95% of valid webhook events produce normalized interaction records within 60 seconds of receipt in test and staging environments.
- **SC-003**: At least 95% of keyword-matching interactions create automation runs and outgoing actions without manual intervention.
- **SC-004**: 100% of outgoing actions end in a terminal status or explicit retry-pending state with a recorded result reason.
- **SC-005**: 100% of critical V1 pipeline behaviors listed in this spec have passing automated Pest tests.

## Assumptions

- Authentication and workspace membership controls already exist or are provided by current project auth foundations.
- V1 supports a single fake provider behavior profile for deterministic testing.
- Queue infrastructure is available in target environments for asynchronous jobs.
- Real payment and subscription enforcement are out of scope for this feature.
- A simple dashboard/admin surface is sufficient for V1 operational visibility; advanced workflow design UX is deferred.
