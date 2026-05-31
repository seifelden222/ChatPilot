---
description: "Task list template for feature implementation"
---

# Tasks: [FEATURE NAME]

**Input**: Design documents from `/specs/[###-feature-name]/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Pest tests are REQUIRED for important core behaviors. Include test tasks for each user story and foundational workflow behavior.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Laravel app**: `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `tests/`
- **Core backend logic**: `app/Actions`, `app/Services`, `app/Events`, `app/Listeners`, `app/Jobs`, `app/Observers`, `app/Support`, `app/Models`
- **Tests**: `tests/Feature`, `tests/Unit` (Pest)
- Include exact project paths from the implementation plan.

<!--
  ============================================================================
  IMPORTANT: The tasks below are SAMPLE TASKS for illustration purposes only.

  The /speckit.tasks command MUST replace these with actual tasks based on:
  - User stories from spec.md (with their priorities P1, P2, P3...)
  - Feature requirements from plan.md
  - Entities from data-model.md
  - Endpoints from contracts/

  Tasks MUST be organized by user story so each story can be:
  - Implemented independently
  - Tested independently
  - Delivered as an MVP increment

  DO NOT keep these sample tasks in the generated tasks.md file.
  ============================================================================
-->

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [ ] T001 Confirm Laravel project structure and module boundaries from plan
- [ ] T002 Configure required dependencies and environment settings
- [ ] T003 [P] Configure linting/formatting and test commands

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**CRITICAL**: No user story work can begin until this phase is complete

Examples of foundational tasks (adjust based on your project):

- [ ] T004 Create/update migrations and baseline indexes for core entities
- [ ] T005 [P] Implement shared enums/DTOs/support utilities
- [ ] T006 [P] Establish queue/event pipeline scaffolding
- [ ] T007 Implement base models/relationships needed by all stories
- [ ] T008 Configure error handling, logging, and observability
- [ ] T009 Add foundational Pest tests for critical workflow invariants

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - [Title] (Priority: P1) MVP

**Goal**: [Brief description of what this story delivers]

**Independent Test**: [How to verify this story works on its own]

### Tests for User Story 1 (REQUIRED)

- [ ] T010 [P] [US1] Feature test for [workflow] in tests/Feature/[Name]Test.php
- [ ] T011 [P] [US1] Unit test for [service/action] in tests/Unit/[Name]Test.php

### Implementation for User Story 1

- [ ] T012 [P] [US1] Create/update [Entity1] model in app/Models/[Entity1].php
- [ ] T013 [P] [US1] Create/update [Entity2] model in app/Models/[Entity2].php
- [ ] T014 [US1] Implement [Service] in app/Services/[Service].php (depends on T012, T013)
- [ ] T015 [US1] Implement [feature entrypoint] in app/Http/Controllers/[Controller].php or app/Livewire/[Component].php
- [ ] T016 [US1] Add validation and error handling
- [ ] T017 [US1] Add domain events/listeners or jobs where needed

**Checkpoint**: User Story 1 should be fully functional and independently testable

---

## Phase 4: User Story 2 - [Title] (Priority: P2)

**Goal**: [Brief description of what this story delivers]

**Independent Test**: [How to verify this story works on its own]

### Tests for User Story 2 (REQUIRED)

- [ ] T018 [P] [US2] Feature test for [workflow] in tests/Feature/[Name]Test.php
- [ ] T019 [P] [US2] Unit test for [service/action] in tests/Unit/[Name]Test.php

### Implementation for User Story 2

- [ ] T020 [P] [US2] Create/update [Entity] model in app/Models/[Entity].php
- [ ] T021 [US2] Implement [Service] in app/Services/[Service].php
- [ ] T022 [US2] Implement [feature entrypoint] in app/Livewire/[Component].php or app/Filament/[ResourceOrPage].php
- [ ] T023 [US2] Integrate with existing US1 components where required

**Checkpoint**: User Stories 1 and 2 should both work independently

---

## Phase 5: User Story 3 - [Title] (Priority: P3)

**Goal**: [Brief description of what this story delivers]

**Independent Test**: [How to verify this story works on its own]

### Tests for User Story 3 (REQUIRED)

- [ ] T024 [P] [US3] Feature test for [workflow] in tests/Feature/[Name]Test.php
- [ ] T025 [P] [US3] Unit test for [service/action] in tests/Unit/[Name]Test.php

### Implementation for User Story 3

- [ ] T026 [P] [US3] Create/update [Entity] model in app/Models/[Entity].php
- [ ] T027 [US3] Implement [Service] in app/Services/[Service].php
- [ ] T028 [US3] Implement [feature entrypoint] in app/Filament/[ResourceOrPage].php or app/Livewire/[Component].php

**Checkpoint**: All user stories should now be independently functional

---

## Phase N: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [ ] TXXX [P] Documentation updates in docs/
- [ ] TXXX Code cleanup and refactoring
- [ ] TXXX Performance optimization across all stories
- [ ] TXXX [P] Additional Pest coverage in tests/Feature/ or tests/Unit/
- [ ] TXXX Security hardening
- [ ] TXXX Run quickstart validation

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2)
- **User Story 2 (P2)**: Can start after Foundational (Phase 2)
- **User Story 3 (P3)**: Can start after Foundational (Phase 2)

### Within Each User Story

- Tests for important behavior MUST exist before story completion
- Models before services
- Services before entrypoints
- Core implementation before integration
- Story complete before moving to next priority

### Parallel Opportunities

- Tasks marked [P] can run in parallel when they touch different files
- After Foundational phase completion, user stories can proceed in parallel

---

## Parallel Example: User Story 1

```bash
Task: "Feature test for [workflow] in tests/Feature/[Name]Test.php"
Task: "Unit test for [service/action] in tests/Unit/[Name]Test.php"
Task: "Create/update [Entity1] model in app/Models/[Entity1].php"
Task: "Create/update [Entity2] model in app/Models/[Entity2].php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational
3. Complete Phase 3: User Story 1
4. Validate User Story 1 independently
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational
2. Add User Story 1 -> test independently -> deploy/demo
3. Add User Story 2 -> test independently -> deploy/demo
4. Add User Story 3 -> test independently -> deploy/demo

### Parallel Team Strategy

1. Team completes Setup + Foundational together
2. Then stories can be implemented by separate developers in parallel
3. Integrate only after each story passes independent tests

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story
- Each user story should be independently completable and testable
- Commit after each logical task group
- Avoid vague tasks and cross-story file conflicts
