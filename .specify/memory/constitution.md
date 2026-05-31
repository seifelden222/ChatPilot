<!--
Sync Impact Report
- Version change: N/A -> 1.0.0
- Modified principles:
	- N/A -> I. Laravel-First Architecture
	- N/A -> II. Provider-Neutral Domain Core
	- N/A -> III. V1 Integration Boundary
	- N/A -> IV. Event-Driven, Queue-First Workflow
	- N/A -> V. Data Model Integrity and Future Expansion
- Added sections:
	- V1 Scope Boundaries
	- Delivery Workflow and Safety Gates
- Removed sections:
	- None
- Templates requiring updates:
	- ✅ updated: .specify/templates/plan-template.md
	- ✅ updated: .specify/templates/spec-template.md
	- ✅ updated: .specify/templates/tasks-template.md
	- ⚠ pending: .specify/templates/commands/*.md (directory not present)
- Deferred TODOs:
	- None
-->

# ChatPilot Constitution

## Core Principles

### I. Laravel-First Architecture
ChatPilot MUST follow Laravel 13 conventions and ecosystem defaults. Interactive
product UI MUST be implemented with Livewire, and admin or operational dashboards
MUST be implemented with Filament where applicable. Controllers, Filament
resources, and Livewire components MUST remain thin orchestration layers, while
business logic MUST live in Actions, Services, Jobs, Events, Listeners,
Observers, and Support classes. Pest is the required test framework for automated
verification.

Rationale: Framework-consistent architecture reduces complexity and preserves
maintainability as the platform scales.

### II. Provider-Neutral Domain Core
Core domain design MUST remain channel-neutral and MUST NOT encode provider names
into core entities or workflows. The canonical domain vocabulary is: Channel,
Actor, ActorIdentity, ContentItem, Thread, Interaction, Automation,
AutomationTrigger, AutomationStep, AutomationRun, AutomationRunStep,
OutgoingAction, WebhookEvent, and Tag. Platform-specific table names such as
instagram_comments, whatsapp_messages, or facebook_posts are prohibited in the
core schema.

Rationale: Neutral modeling enables multi-provider expansion without rewriting
business rules.

### III. V1 Integration Boundary
V1 MUST use a single SocialIntegrationService as the only location for
provider-specific logic. The rest of the codebase MUST NOT include
provider-specific conditionals or branching. V1 MUST NOT introduce provider
adapter interfaces or dedicated provider classes yet; that decomposition is
explicitly deferred to a later version.

Rationale: A single boundary keeps V1 delivery focused while preserving a clean
future refactor path.

### IV. Event-Driven, Queue-First Workflow
Webhook endpoints MUST acknowledge requests quickly and offload heavy work to
queued jobs. Raw webhook payloads MUST be stored in webhook_events before
processing. Domain reactions MUST be modeled with Events and Listeners; Observers
MUST be limited to lightweight model lifecycle concerns. External API calls MUST
NOT run inside controllers, observers, Filament pages, or Livewire components.
The V1 core pipeline MUST follow this sequence:

Webhook received -> store WebhookEvent -> dispatch ProcessWebhookEventJob ->
SocialIntegrationService normalizes payload into NormalizedEventData ->
InteractionService creates or updates Actor, ActorIdentity, ContentItem, Thread,
Interaction -> InteractionCreated event -> MatchAutomationForInteraction listener
evaluates triggers -> AutomationRunnerService creates AutomationRun and
OutgoingAction records -> OutgoingActionCreated event ->
ExecuteOutgoingActionJob executes action through SocialIntegrationService ->
outgoing action and interaction or thread statuses updated.

Rationale: Queue-first, event-driven processing improves reliability, throughput,
and fault isolation.

### V. Data Model Integrity and Future Expansion
Important searchable fields MUST be stored in first-class columns, not hidden in
JSON blobs. JSON MAY be used only for flexible provider metadata, payloads,
settings, capabilities, conditions, attachments, and provider responses.
Schema and indexing decisions MUST support future provider expansion and
cross-channel querying.

Rationale: Strong relational structure is required for performance, reporting, and
evolvability.

## V1 Scope Boundaries

V1 MUST implement only the provider-neutral core system using a fake or test
provider. Real Instagram, Facebook, WhatsApp, Telegram, or other production
provider APIs are out of scope for V1. V1 scope includes:

- Workspaces
- Channels
- Actors
- Actor identities
- Content items
- Threads
- Interactions
- Automations
- Automation triggers
- Automation steps
- Automation runs
- Outgoing actions
- Webhook events
- Tags
- Fake provider webhook testing
- Keyword automation matching
- Queued outgoing action execution

V1 MUST NOT include a visual flow builder, real billing, or full provider API
integrations.

## Delivery Workflow and Safety Gates

Implementation MUST use migrations, models, relationships, casts, enums,
services, actions, jobs, events, listeners, and Pest tests where appropriate.
Every important core behavior MUST have automated Pest coverage. Code MUST prefer
simple, reliable, and testable solutions over speculative abstractions.

Before broad multi-file changes, teams MUST create a clear implementation plan and
tasks. Database changes with destructive impact MUST be explicitly described,
justified, and reviewed before execution.

Target organization for core backend code is:

- app/Actions
- app/Services
- app/Events
- app/Listeners
- app/Jobs
- app/Observers
- app/Support/Enums
- app/Support/DTOs
- app/Support/Helpers
- app/Models

## Governance

This constitution is the highest-priority engineering policy for ChatPilot and
supersedes conflicting local practices.

- Amendment process: changes MUST be proposed in writing, reviewed by project
	maintainers, and merged with explicit rationale and migration impact notes.
- Versioning policy: constitution versioning follows semantic versioning.
	MAJOR = incompatible governance or principle redefinition/removal.
	MINOR = new principle/section or materially expanded guidance.
	PATCH = clarifications, wording improvements, or non-semantic edits.
- Compliance reviews: every implementation plan, specification, and task list
	MUST include a constitution compliance check before execution and at PR review.
- Enforcement: pull requests that violate non-negotiable principles MUST NOT be
	approved without an accepted amendment.

**Version**: 1.0.0 | **Ratified**: 2026-05-31 | **Last Amended**: 2026-05-31
