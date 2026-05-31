# Tasks: ChatPilot V1 Core System

**Input**: Design documents from `/specs/001-chatpilot-v1-core/`

**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/, quickstart.md

**Tests**: Pest tests are mandatory for critical core behaviors in every task group.

**Organization**: Tasks are grouped by implementation phase and mapped to user stories where applicable.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: User story label for story-phase tasks (`[US1]`, `[US2]`, `[US3]`)
- All task descriptions include concrete file paths.

## Phase 1: Setup (Shared Infrastructure)

**Task Group**: V1-01 Domain Foundation  
**Branch**: `feature/v1-01-domain-foundation`  
**Expected files**: `app/Support/Enums/*`, `app/Support/DTOs/NormalizedEventData.php`, `app/Services/*`, `app/Actions/*`, `tests/Unit/Support/*`  
**Pest tests**: `tests/Unit/Support/NormalizedEventDataTest.php`, `tests/Unit/Support/EnumConventionsTest.php`  
**Verification commands**: `php artisan test --compact --filter=NormalizedEventDataTest`, `php artisan test --compact --filter=EnumConventionsTest`, `vendor/bin/pint --dirty --format agent`

- [x] T001 Create branch `feature/v1-01-domain-foundation` for updates in specs/001-chatpilot-v1-core/tasks.md
- [x] T002 Create provider-neutral enums in app/Support/Enums/ChannelType.php
- [x] T003 [P] Create provider-neutral enums in app/Support/Enums/InteractionDirection.php
- [x] T004 [P] Create provider-neutral enums in app/Support/Enums/WebhookEventStatus.php
- [x] T005 [P] Create provider-neutral enums in app/Support/Enums/AutomationRunStatus.php
- [x] T006 [P] Create provider-neutral enums in app/Support/Enums/OutgoingActionStatus.php
- [x] T007 Create DTO for normalized inbound payload in app/Support/DTOs/NormalizedEventData.php
- [x] T008 [P] Create service stub for provider-neutral ingestion logic in app/Services/InteractionService.php
- [x] T009 [P] Create service stub for automation execution orchestration in app/Services/AutomationRunnerService.php
- [x] T010 [P] Create action stub for actor upsert in app/Actions/UpsertActorAction.php
- [x] T011 [P] Create action stub for identity upsert in app/Actions/UpsertActorIdentityAction.php
- [x] T012 Add unit tests for normalized DTO rules in tests/Unit/Support/NormalizedEventDataTest.php
- [x] T013 Add unit tests for enum domain conventions in tests/Unit/Support/EnumConventionsTest.php
- [x] T014 Run verification commands for phase in specs/001-chatpilot-v1-core/quickstart.md
- [x] T015 Commit group changes for files under app/Support/ and tests/Unit/Support/ in specs/001-chatpilot-v1-core/tasks.md
- [x] T016 Push branch `feature/v1-01-domain-foundation` after tests pass in specs/001-chatpilot-v1-core/tasks.md

---

## Phase 2: Foundational (Blocking Prerequisites)

### Task Group: V1-02 Database Schema

**Branch**: `feature/v1-02-database-schema`  
**Expected files**: `database/migrations/*`, `tests/Feature/Database/CoreSchemaTest.php`  
**Pest tests**: `tests/Feature/Database/CoreSchemaTest.php`  
**Verification commands**: `php artisan test --compact --filter=CoreSchemaTest`, `php artisan migrate:fresh --env=testing`, `vendor/bin/pint --dirty --format agent`

- [ ] T017 Create branch `feature/v1-02-database-schema` for migration work in database/migrations/
- [ ] T018 Create workspace and channel migrations in database/migrations/*_create_workspaces_table.php
- [ ] T019 [P] Create actor and identity migrations in database/migrations/*_create_actor_identities_table.php
- [ ] T020 [P] Create content and thread migrations in database/migrations/*_create_threads_table.php
- [ ] T021 Create interaction migration with uniqueness and indexes in database/migrations/*_create_interactions_table.php
- [ ] T022 [P] Create automation core migrations in database/migrations/*_create_automation_steps_table.php
- [ ] T023 [P] Create automation run and run step migrations in database/migrations/*_create_automation_run_steps_table.php
- [ ] T024 [P] Create outgoing action migration in database/migrations/*_create_outgoing_actions_table.php
- [ ] T025 [P] Create webhook event migration in database/migrations/*_create_webhook_events_table.php
- [ ] T026 [P] Create tags migration in database/migrations/*_create_tags_table.php
- [ ] T027 Add schema/index/constraint assertions in tests/Feature/Database/CoreSchemaTest.php
- [ ] T028 Run verification commands for phase in specs/001-chatpilot-v1-core/quickstart.md
- [ ] T029 Commit schema group for files in database/migrations/ and tests/Feature/Database/CoreSchemaTest.php
- [ ] T030 Push branch `feature/v1-02-database-schema` after tests pass in specs/001-chatpilot-v1-core/tasks.md

### Task Group: V1-03 Models and Observers

**Branch**: `feature/v1-03-models-observers`  
**Expected files**: `app/Models/*`, `app/Observers/*`, `app/Providers/AppServiceProvider.php`, `tests/Unit/Models/*`  
**Pest tests**: `tests/Unit/Models/CoreModelRelationshipTest.php`, `tests/Unit/Models/CoreModelCastsTest.php`  
**Verification commands**: `php artisan test --compact --filter=CoreModelRelationshipTest`, `php artisan test --compact --filter=CoreModelCastsTest`, `vendor/bin/pint --dirty --format agent`

- [ ] T031 Create branch `feature/v1-03-models-observers` for model layer updates in app/Models/
- [ ] T032 Create workspace-channel-actor model classes in app/Models/Workspace.php
- [ ] T033 [P] Create identity-content-thread model classes in app/Models/Thread.php
- [ ] T034 Create interaction model with casts and scopes in app/Models/Interaction.php
- [ ] T035 [P] Create automation core model classes in app/Models/AutomationStep.php
- [ ] T036 [P] Create automation run and outgoing action model classes in app/Models/OutgoingAction.php
- [ ] T037 [P] Create webhook event and tag model classes in app/Models/WebhookEvent.php
- [ ] T038 Create lightweight observers in app/Observers/WebhookEventObserver.php
- [ ] T039 Register observers in app/Providers/AppServiceProvider.php
- [ ] T040 Add relationship tests in tests/Unit/Models/CoreModelRelationshipTest.php
- [ ] T041 Add cast/state tests in tests/Unit/Models/CoreModelCastsTest.php
- [ ] T042 Run verification commands for phase in specs/001-chatpilot-v1-core/quickstart.md
- [ ] T043 Commit model group for app/Models/, app/Observers/, and tests/Unit/Models/
- [ ] T044 Push branch `feature/v1-03-models-observers` after tests pass in specs/001-chatpilot-v1-core/tasks.md

**Checkpoint**: Foundation ready; user story work can proceed.

---

## Phase 3: User Story 1 - Ingest and Normalize Webhook Interactions (Priority: P1) MVP

**Goal**: Accept fake webhook events, persist raw data, normalize payloads, and create interaction records.

**Independent Test**: POST fake webhook payload and verify WebhookEvent + Actor + ActorIdentity + ContentItem + Thread + Interaction effects.

### Task Group: V1-04 Webhook Pipeline

**Branch**: `feature/v1-04-webhook-pipeline`  
**Expected files**: `routes/api.php` or `routes/web.php`, `app/Http/Controllers/FakeWebhookController.php`, `app/Jobs/ProcessWebhookEventJob.php`, `app/Services/SocialIntegrationService.php`, `app/Services/InteractionService.php`, `app/Events/InteractionCreated.php`, `tests/Feature/Webhooks/*`, `tests/Unit/Services/SocialIntegrationServiceNormalizationTest.php`  
**Pest tests**: `tests/Feature/Webhooks/FakeWebhookIngestionTest.php`, `tests/Feature/Webhooks/WebhookDeduplicationTest.php`, `tests/Feature/Webhooks/InteractionCreationPipelineTest.php`, `tests/Unit/Services/SocialIntegrationServiceNormalizationTest.php`  
**Verification commands**: `php artisan test --compact --filter=FakeWebhookIngestionTest`, `php artisan test --compact --filter=WebhookDeduplicationTest`, `php artisan test --compact --filter=SocialIntegrationServiceNormalizationTest`, `vendor/bin/pint --dirty --format agent`

- [ ] T045 [US1] Create branch `feature/v1-04-webhook-pipeline` for webhook pipeline files in app/Http/Controllers/
- [ ] T046 [US1] Add fake webhook route contract in routes/api.php
- [ ] T047 [US1] Implement thin webhook controller in app/Http/Controllers/FakeWebhookController.php
- [ ] T048 [US1] Implement queue-first webhook processing job in app/Jobs/ProcessWebhookEventJob.php
- [ ] T049 [US1] Implement fake provider normalization inside app/Services/SocialIntegrationService.php
- [ ] T050 [US1] Implement interaction upsert orchestration in app/Services/InteractionService.php
- [ ] T051 [US1] Add InteractionCreated event in app/Events/InteractionCreated.php
- [ ] T052 [P] [US1] Add ingestion feature tests in tests/Feature/Webhooks/FakeWebhookIngestionTest.php
- [ ] T053 [P] [US1] Add deduplication tests in tests/Feature/Webhooks/WebhookDeduplicationTest.php
- [ ] T054 [P] [US1] Add interaction pipeline tests in tests/Feature/Webhooks/InteractionCreationPipelineTest.php
- [ ] T055 [P] [US1] Add normalization unit tests in tests/Unit/Services/SocialIntegrationServiceNormalizationTest.php
- [ ] T056 [US1] Run verification commands for webhook group in specs/001-chatpilot-v1-core/quickstart.md
- [ ] T057 [US1] Commit webhook group for app/Http/Controllers/, app/Jobs/, app/Services/, tests/Feature/Webhooks/
- [ ] T058 [US1] Push branch `feature/v1-04-webhook-pipeline` after tests pass in specs/001-chatpilot-v1-core/tasks.md

**Checkpoint**: US1 is independently testable and shippable.

---

## Phase 4: User Story 2 - Trigger and Run Keyword Automations (Priority: P2)

**Goal**: Match keyword triggers on new interactions and produce automation runs, run steps, and outgoing actions.

**Independent Test**: Create automations and interactions, then verify matching and non-matching outcomes.

### Task Group: V1-05 Automation Core

**Branch**: `feature/v1-05-automation-core`  
**Expected files**: `app/Listeners/MatchAutomationForInteraction.php`, `app/Services/AutomationRunnerService.php`, `app/Actions/CreateAutomationRunAction.php`, `app/Actions/CreateOutgoingActionAction.php`, `app/Events/OutgoingActionCreated.php`, `tests/Feature/Automation/*`  
**Pest tests**: `tests/Feature/Automation/KeywordAutomationMatchingTest.php`, `tests/Feature/Automation/AutomationRunCreationTest.php`, `tests/Feature/Automation/OutgoingActionCreationTest.php`  
**Verification commands**: `php artisan test --compact --filter=KeywordAutomationMatchingTest`, `php artisan test --compact --filter=AutomationRunCreationTest`, `php artisan test --compact --filter=OutgoingActionCreationTest`, `vendor/bin/pint --dirty --format agent`

- [ ] T059 [US2] Create branch `feature/v1-05-automation-core` for automation core files in app/Services/
- [ ] T060 [US2] Implement interaction listener for keyword matching in app/Listeners/MatchAutomationForInteraction.php
- [ ] T061 [US2] Implement automation runner service in app/Services/AutomationRunnerService.php
- [ ] T062 [P] [US2] Implement automation run creation action in app/Actions/CreateAutomationRunAction.php
- [ ] T063 [P] [US2] Implement run step creation action in app/Actions/CreateAutomationRunStepAction.php
- [ ] T064 [P] [US2] Implement outgoing action creation action in app/Actions/CreateOutgoingActionAction.php
- [ ] T065 [US2] Emit OutgoingActionCreated event from runner in app/Events/OutgoingActionCreated.php
- [ ] T066 [P] [US2] Add keyword matching tests in tests/Feature/Automation/KeywordAutomationMatchingTest.php
- [ ] T067 [P] [US2] Add automation run tests in tests/Feature/Automation/AutomationRunCreationTest.php
- [ ] T068 [P] [US2] Add outgoing action creation tests in tests/Feature/Automation/OutgoingActionCreationTest.php
- [ ] T069 [US2] Run verification commands for automation group in specs/001-chatpilot-v1-core/quickstart.md
- [ ] T070 [US2] Commit automation group for app/Listeners/, app/Services/, app/Actions/, tests/Feature/Automation/
- [ ] T071 [US2] Push branch `feature/v1-05-automation-core` after tests pass in specs/001-chatpilot-v1-core/tasks.md

**Checkpoint**: US2 is independently testable and shippable.

---

## Phase 5: User Story 3 - Execute Outgoing Actions Asynchronously (Priority: P3)

**Goal**: Queue outgoing action execution through fake provider support and track status transitions.

**Independent Test**: Trigger outgoing actions and verify queue dispatch, success/failure status updates, and provider response capture.

### Task Group: V1-06 Outgoing Actions

**Branch**: `feature/v1-06-outgoing-actions`  
**Expected files**: `app/Listeners/QueueOutgoingActionExecution.php`, `app/Jobs/ExecuteOutgoingActionJob.php`, `app/Services/SocialIntegrationService.php`, `tests/Feature/OutgoingActions/*`, `tests/Unit/Services/SocialIntegrationServiceExecuteActionTest.php`  
**Pest tests**: `tests/Feature/OutgoingActions/QueuedExecutionSuccessTest.php`, `tests/Feature/OutgoingActions/QueuedExecutionFailureTest.php`, `tests/Feature/OutgoingActions/OutgoingActionStatusTransitionTest.php`, `tests/Unit/Services/SocialIntegrationServiceExecuteActionTest.php`  
**Verification commands**: `php artisan test --compact --filter=QueuedExecution`, `php artisan test --compact --filter=OutgoingActionStatusTransitionTest`, `php artisan test --compact --filter=SocialIntegrationServiceExecuteActionTest`, `vendor/bin/pint --dirty --format agent`

- [ ] T072 [US3] Create branch `feature/v1-06-outgoing-actions` for queued execution files in app/Jobs/
- [ ] T073 [US3] Implement outgoing action queue listener in app/Listeners/QueueOutgoingActionExecution.php
- [ ] T074 [US3] Implement queued action execution job in app/Jobs/ExecuteOutgoingActionJob.php
- [ ] T075 [US3] Implement fake provider execution path in app/Services/SocialIntegrationService.php
- [ ] T076 [P] [US3] Add success-path feature tests in tests/Feature/OutgoingActions/QueuedExecutionSuccessTest.php
- [ ] T077 [P] [US3] Add failure-path feature tests in tests/Feature/OutgoingActions/QueuedExecutionFailureTest.php
- [ ] T078 [P] [US3] Add status transition tests in tests/Feature/OutgoingActions/OutgoingActionStatusTransitionTest.php
- [ ] T079 [P] [US3] Add service execute unit tests in tests/Unit/Services/SocialIntegrationServiceExecuteActionTest.php
- [ ] T080 [US3] Run verification commands for outgoing-action group in specs/001-chatpilot-v1-core/quickstart.md
- [ ] T081 [US3] Commit outgoing action group for app/Listeners/, app/Jobs/, app/Services/, tests/Feature/OutgoingActions/
- [ ] T082 [US3] Push branch `feature/v1-06-outgoing-actions` after tests pass in specs/001-chatpilot-v1-core/tasks.md

**Checkpoint**: US3 is independently testable and shippable.

---

## Phase 6: Polish & Cross-Cutting Concerns

### Task Group: V1-07 Tests and Docs

**Branch**: `feature/v1-07-tests-docs`  
**Expected files**: `tests/Feature/Pipeline/V1EndToEndPipelineTest.php`, `tests/Feature/Dashboard/OperationalVisibilityTest.php`, `database/factories/*`, `app/Filament/**` or `app/Livewire/**` (minimal), `README.md`  
**Pest tests**: `tests/Feature/Pipeline/V1EndToEndPipelineTest.php`, `tests/Feature/Dashboard/OperationalVisibilityTest.php`  
**Verification commands**: `php artisan test --compact --filter=V1EndToEndPipelineTest`, `php artisan test --compact --filter=OperationalVisibilityTest`, `php artisan test --compact`, `vendor/bin/pint --dirty --format agent`

- [ ] T083 Create branch `feature/v1-07-tests-docs` for final test/doc updates in tests/Feature/
- [ ] T084 Add end-to-end core pipeline test in tests/Feature/Pipeline/V1EndToEndPipelineTest.php
- [ ] T085 [P] Add minimal operational dashboard visibility test in tests/Feature/Dashboard/OperationalVisibilityTest.php
- [ ] T086 [P] Add required factories for pipeline scenarios in database/factories/
- [ ] T087 Add minimal Filament or Livewire operational surface in app/Filament/ or app/Livewire/
- [ ] T088 Update V1 workflow documentation in README.md
- [ ] T089 Run full verification commands for final group in specs/001-chatpilot-v1-core/quickstart.md
- [ ] T090 Commit final group for tests/Feature/, database/factories/, app/Filament/ or app/Livewire/, README.md
- [ ] T091 Push branch `feature/v1-07-tests-docs` after tests pass in specs/001-chatpilot-v1-core/tasks.md
- [ ] T092 Confirm no merge occurs until branch tests pass in specs/001-chatpilot-v1-core/tasks.md

---

## Dependencies & Execution Order

### Phase Dependencies

- Setup phase must complete before foundational phase.
- Foundational phase blocks all story phases.
- US1 (`feature/v1-04-webhook-pipeline`) must complete before US2.
- US2 (`feature/v1-05-automation-core`) must complete before US3.
- Final polish phase depends on US1, US2, and US3 completion.

### User Story Dependencies

- **US1**: Depends on V1-01, V1-02, V1-03.
- **US2**: Depends on US1 and foundational groups.
- **US3**: Depends on US2 and foundational groups.

### Git Workflow Dependencies

- Branch per task group must be created before implementation tasks.
- Tests and verification commands must pass before group commit.
- Group commit must occur before push to origin.
- No merge action is allowed for a group unless tests pass.

## Parallel Opportunities

- Phase 1 enum files (`app/Support/Enums/*`) and action/service stubs can be implemented in parallel.
- Phase 2 independent migration files can be authored in parallel before final schema test integration.
- US1 feature tests and normalization unit tests can be written in parallel.
- US2 action creation tasks and automation tests can run in parallel.
- US3 success/failure test files can run in parallel.

## Parallel Example: US1

- [ ] T093 [P] [US1] Draft ingestion assertions in tests/Feature/Webhooks/FakeWebhookIngestionTest.php while normalization logic is built in app/Services/SocialIntegrationService.php
- [ ] T094 [P] [US1] Draft deduplication assertions in tests/Feature/Webhooks/WebhookDeduplicationTest.php while queue job logic is built in app/Jobs/ProcessWebhookEventJob.php

## Parallel Example: US2

- [ ] T095 [P] [US2] Implement run-step action in app/Actions/CreateAutomationRunStepAction.php while keyword listener is implemented in app/Listeners/MatchAutomationForInteraction.php
- [ ] T096 [P] [US2] Implement automation run tests in tests/Feature/Automation/AutomationRunCreationTest.php while outgoing action creation is implemented in app/Actions/CreateOutgoingActionAction.php

## Parallel Example: US3

- [ ] T097 [P] [US3] Implement success-path tests in tests/Feature/OutgoingActions/QueuedExecutionSuccessTest.php while failure-path tests are implemented in tests/Feature/OutgoingActions/QueuedExecutionFailureTest.php
- [ ] T098 [P] [US3] Implement queue listener in app/Listeners/QueueOutgoingActionExecution.php while job handling is implemented in app/Jobs/ExecuteOutgoingActionJob.php

## Implementation Strategy

### MVP First (US1)

1. Complete V1-01, V1-02, V1-03.
2. Complete V1-04 (US1 webhook pipeline).
3. Validate US1 independently with webhook-focused tests.

### Incremental Delivery

1. Add V1-05 (US2 automation core) after US1 stability.
2. Add V1-06 (US3 outgoing actions) after US2 stability.
3. Add V1-07 (final tests/docs and minimal ops UI) before release.

### Safety Guardrails

- Keep provider logic only inside app/Services/SocialIntegrationService.php.
- Maintain provider-neutral naming across app/Models/ and database/migrations/.
- Exclude real Instagram/Facebook/WhatsApp/Telegram APIs, billing, and visual flow builder from all task groups.
