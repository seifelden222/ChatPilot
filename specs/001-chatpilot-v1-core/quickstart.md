# Quickstart: ChatPilot V1 Core Implementation

## Preconditions

- Branch: `001-setup-feature-branch`
- Spec: `specs/001-chatpilot-v1-core/spec.md`
- Plan: `specs/001-chatpilot-v1-core/plan.md`

## Workflow

1. Implement phases sequentially from the plan to keep review scope safe.
2. Keep each PR focused to one phase where possible.
3. Run only targeted tests for the current phase first.
4. Run full test suite before merging late phases.

## Phase Execution Commands (reference)

- Create files/classes via Artisan where applicable:
  - `php artisan make:model`
  - `php artisan make:migration`
  - `php artisan make:event`
  - `php artisan make:listener`
  - `php artisan make:job`
  - `php artisan make:test --pest`
- Run focused tests:
  - `php artisan test --compact --filter=Webhook`
  - `php artisan test --compact --filter=Automation`
  - `php artisan test --compact --filter=OutgoingAction`
- Run formatting after PHP changes:
  - `vendor/bin/pint --dirty --format agent`

## Validation Milestones

- After Phase 2: schema tests pass and indexes/unique constraints verified.
- After Phase 5: event dispatch and queue boundaries verified.
- After Phase 9: outgoing action status transitions verified under success and failure.
- After Phase 11: end-to-end V1 pipeline test passes consistently.
- After Phase 12: full test suite and formatting pass.

## Non-Goals Guardrails

- Do not add real provider integrations.
- Do not add billing flows.
- Do not add visual flow builder.
- Do not add provider interface/adapter abstractions in V1.

## Handoff Criteria

- All required phase tests pass.
- Constitution check remains fully satisfied.
- Pipeline behavior matches contracts in `specs/001-chatpilot-v1-core/contracts/`.
