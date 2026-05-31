# Implementation Plan: ChatPilot V1 Core System

**Branch**: `001-setup-feature-branch` | **Date**: 2026-05-31 | **Spec**: `/specs/001-chatpilot-v1-core/spec.md`

**Input**: Feature specification from `/specs/001-chatpilot-v1-core/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/plan-template.md` for the execution workflow.

## Summary

Build the provider-neutral ChatPilot V1 core pipeline on Laravel 13 using a
fake provider only: ingest webhook payloads, normalize events, create or update
interaction domain records, match keyword automations, create outgoing actions,
and execute them asynchronously. Delivery is split into 12 review-safe phases
with explicit file scopes and mandatory Pest verification at each stage.

## Technical Context

<!--
  ACTION REQUIRED: Replace the content in this section with the technical details
  for the project. The structure here is presented in advisory capacity to guide
  the iteration process.
-->

**Language/Version**: PHP 8.4, Laravel 13

**Primary Dependencies**: Laravel 13, Livewire, Filament, Pest, queues/events, Eloquent

**Storage**: Relational DB (MySQL/PostgreSQL), relational-first schema with constrained JSON columns

**Testing**: Pest feature and unit tests for each core pipeline invariant

**Target Platform**: Laravel SaaS web application with queue workers

**Project Type**: Laravel monolith with provider-neutral automation core

**Performance Goals**: Webhook responses are fast (accepted/queued), async processing completes within target SLOs from spec

**Constraints**: No real provider APIs, no billing, no visual flow builder, no provider interfaces/adapters in V1

**Scale/Scope**: V1 provider-neutral core entities + fake provider end-to-end pipeline only

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [x] Laravel-first structure is preserved (thin controllers/Livewire/Filament,
      business logic in Actions/Services/Jobs/Events/Listeners/Observers/Support)
- [x] Provider-neutral domain naming is used; no provider-specific core tables or
      cross-cutting conditionals outside SocialIntegrationService
- [x] Event-driven queue-first flow is designed (webhook storage, async processing,
      no external API calls from UI/controller layers)
- [x] Data model keeps searchable fields as columns; JSON only for flexible
      provider metadata/payload/config payloads
- [x] V1 scope boundaries are respected (fake provider allowed; real provider APIs,
      visual flow builder, and real billing excluded)
- [x] Pest testing strategy covers all important core behaviors

## Project Structure

### Documentation (this feature)

```text
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)
<!--
  ACTION REQUIRED: Replace the placeholder tree below with the concrete layout
  for this feature. Delete unused options and expand the chosen structure with
  real paths (e.g., apps/admin, packages/something). The delivered plan must
  not include Option labels.
-->

```text
app/
├── Actions/
├── Services/
├── Events/
├── Listeners/
├── Jobs/
├── Observers/
├── Support/
│   ├── DTOs/
│   └── Enums/
├── Models/
└── Http/Controllers/

database/
├── migrations/
└── factories/

routes/
├── web.php
└── [api routes if added]

tests/
├── Feature/
└── Unit/
```

**Structure Decision**: Use existing Laravel monolith layout and add only the
domain-aligned folders needed for the V1 pipeline.

## Implementation Phases

### Phase 1: Domain enums, DTOs, and folder structure

**Goal**: Establish provider-neutral domain language and foundational code locations.

**Expected files changed**:
- `app/Support/Enums/*`
- `app/Support/DTOs/NormalizedEventData.php`
- `app/Services/` (new service stubs only)
- `app/Actions/` (new action stubs only)

**Required tests**:
- `tests/Unit/Support/NormalizedEventDataTest.php`
- `tests/Unit/Support/EnumConventionsTest.php`

### Phase 2: Database migrations

**Goal**: Add relational-first schema for all V1 core entities with indexes and constrained JSON fields.

**Expected files changed**:
- `database/migrations/*_create_workspaces_table.php`
- `database/migrations/*_create_channels_table.php`
- `database/migrations/*_create_actors_table.php`
- `database/migrations/*_create_actor_identities_table.php`
- `database/migrations/*_create_content_items_table.php`
- `database/migrations/*_create_threads_table.php`
- `database/migrations/*_create_interactions_table.php`
- `database/migrations/*_create_automations_table.php`
- `database/migrations/*_create_automation_triggers_table.php`
- `database/migrations/*_create_automation_steps_table.php`
- `database/migrations/*_create_automation_runs_table.php`
- `database/migrations/*_create_automation_run_steps_table.php`
- `database/migrations/*_create_outgoing_actions_table.php`
- `database/migrations/*_create_webhook_events_table.php`
- `database/migrations/*_create_tags_table.php`

**Required tests**:
- `tests/Feature/Database/CoreSchemaTest.php`

### Phase 3: Eloquent models, casts, relationships, and observers

**Goal**: Implement model layer with strict relationships and lightweight observers.

**Expected files changed**:
- `app/Models/Workspace.php`
- `app/Models/Channel.php`
- `app/Models/Actor.php`
- `app/Models/ActorIdentity.php`
- `app/Models/ContentItem.php`
- `app/Models/Thread.php`
- `app/Models/Interaction.php`
- `app/Models/Automation.php`
- `app/Models/AutomationTrigger.php`
- `app/Models/AutomationStep.php`
- `app/Models/AutomationRun.php`
- `app/Models/AutomationRunStep.php`
- `app/Models/OutgoingAction.php`
- `app/Models/WebhookEvent.php`
- `app/Models/Tag.php`
- `app/Observers/*` (lightweight lifecycle concerns only)
- `app/Providers/AppServiceProvider.php` (observer registration)

**Required tests**:
- `tests/Unit/Models/CoreModelRelationshipTest.php`
- `tests/Unit/Models/CoreModelCastsTest.php`

### Phase 4: Core services and actions

**Goal**: Implement provider-neutral business logic for normalization handoff and interaction persistence orchestration.

**Expected files changed**:
- `app/Services/InteractionService.php`
- `app/Services/AutomationRunnerService.php`
- `app/Actions/*` (entity upsert and run-step creation actions)

**Required tests**:
- `tests/Unit/Services/InteractionServiceTest.php`
- `tests/Unit/Services/AutomationRunnerServiceTest.php`

### Phase 5: Events, listeners, and queued jobs

**Goal**: Wire event-driven orchestration and queue-first execution boundaries.

**Expected files changed**:
- `app/Events/InteractionCreated.php`
- `app/Events/OutgoingActionCreated.php`
- `app/Listeners/MatchAutomationForInteraction.php`
- `app/Listeners/QueueOutgoingActionExecution.php`
- `app/Jobs/ProcessWebhookEventJob.php`
- `app/Jobs/ExecuteOutgoingActionJob.php`

**Required tests**:
- `tests/Feature/Events/InteractionCreatedDispatchTest.php`
- `tests/Feature/Events/OutgoingActionCreatedDispatchTest.php`
- `tests/Feature/Jobs/QueueDispatchTest.php`

### Phase 6: SocialIntegrationService with fake provider support

**Goal**: Centralize all provider-specific conditionals in one service with fake provider capabilities only.

**Expected files changed**:
- `app/Services/SocialIntegrationService.php`
- `app/Support/DTOs/NormalizedEventData.php` (if refinement needed)

**Required tests**:
- `tests/Unit/Services/SocialIntegrationServiceNormalizationTest.php`
- `tests/Unit/Services/SocialIntegrationServiceExecuteActionTest.php`

### Phase 7: Fake webhook endpoint and processing pipeline

**Goal**: Add fake webhook ingress endpoint that stores raw payload then queues processing.

**Expected files changed**:
- `routes/web.php` or `routes/api.php`
- `app/Http/Controllers/FakeWebhookController.php`
- `app/Jobs/ProcessWebhookEventJob.php` (pipeline completion)

**Required tests**:
- `tests/Feature/Webhooks/FakeWebhookIngestionTest.php`
- `tests/Feature/Webhooks/WebhookDeduplicationTest.php`

### Phase 8: Automation matcher and runner

**Goal**: Match keyword triggers and produce automation runs/run steps and outgoing actions.

**Expected files changed**:
- `app/Listeners/MatchAutomationForInteraction.php`
- `app/Services/AutomationRunnerService.php`
- `app/Actions/*` (run + run-step + outgoing-action creation actions)

**Required tests**:
- `tests/Feature/Automation/KeywordAutomationMatchingTest.php`
- `tests/Feature/Automation/AutomationRunCreationTest.php`

### Phase 9: Outgoing action execution pipeline

**Goal**: Execute pending actions asynchronously and persist execution outcomes/status transitions.

**Expected files changed**:
- `app/Jobs/ExecuteOutgoingActionJob.php`
- `app/Listeners/QueueOutgoingActionExecution.php`
- `app/Services/SocialIntegrationService.php` (execute path)

**Required tests**:
- `tests/Feature/OutgoingActions/QueuedExecutionSuccessTest.php`
- `tests/Feature/OutgoingActions/QueuedExecutionFailureTest.php`

### Phase 10: Filament/Livewire minimal dashboard resources if appropriate

**Goal**: Provide minimal operational visibility for V1 pipeline without scope creep.

**Expected files changed**:
- `app/Filament/**` (minimal resources/pages)
- `app/Livewire/**` (only if needed for lightweight operational pages)

**Required tests**:
- `tests/Feature/Dashboard/OperationalVisibilityTest.php`

### Phase 11: Pest tests for the complete V1 core pipeline

**Goal**: Add full integration-level confidence for end-to-end fake provider flow.

**Expected files changed**:
- `tests/Feature/Pipeline/V1EndToEndPipelineTest.php`
- supporting test fixtures/factories in `database/factories/*`

**Required tests**:
- `php artisan test --compact --filter=V1EndToEndPipelineTest`
- targeted suite runs for webhook, automation, and outgoing-action domains

### Phase 12: Final cleanup, docs, and verification

**Goal**: Stabilize naming, remove dead code, verify conventions, and confirm all constraints remain intact.

**Expected files changed**:
- `README.md` (V1 core workflow notes only)
- any touched domain/service files for cleanup

**Required tests**:
- `php artisan test --compact`
- `vendor/bin/pint --dirty --format agent`

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |

## Post-Design Constitution Re-Check

- [x] Laravel-first structure still enforced after phase design.
- [x] Provider-neutral naming and schema constraints remain intact.
- [x] Queue-first event-driven workflow preserved end to end.
- [x] SocialIntegrationService remains the only provider-specific boundary.
- [x] V1 non-goals (real providers, billing, visual flow builder) are excluded.
- [x] Pest testing obligations are included for every critical phase.
