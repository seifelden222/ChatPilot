# Research: ChatPilot V1 Core System

## Decision: Use Laravel monolith domain layering with thin orchestration surfaces

- Rationale: Matches constitution principle I and existing project structure; avoids fragmentation and keeps code review scope manageable.
- Alternatives considered:
  - Split backend into multiple packages: rejected for V1 due to overhead and slower delivery.
  - Put logic inside controllers/components: rejected due to maintainability and testing concerns.

## Decision: Keep provider-specific behavior inside SocialIntegrationService only

- Rationale: Satisfies constitution principle III while keeping a clean future adapter seam.
- Alternatives considered:
  - Introduce provider interface + adapters now: rejected as premature abstraction for V1.
  - Scatter provider conditionals across listeners/jobs/services: rejected because it violates constitution and increases coupling.

## Decision: Queue-first event-driven pipeline for inbound and outbound processing

- Rationale: Aligns with constitution principle IV; resilient for retries, failures, and throughput.
- Alternatives considered:
  - Synchronous webhook processing: rejected due to response latency and fragility.
  - Direct service chaining without events: rejected because it reduces extensibility and observability.

## Decision: Relational-first schema with constrained JSON usage

- Rationale: Supports indexing, reporting, and future provider expansion while preserving flexible payload capture.
- Alternatives considered:
  - JSON-heavy schema for all fields: rejected because searchable business fields become hard to query.
  - Provider-specific tables: rejected by constitution principle II.

## Decision: Minimal V1 UI scope (Filament/Livewire only where operationally useful)

- Rationale: V1 is core-pipeline-first; only lightweight admin visibility is needed to validate workflows.
- Alternatives considered:
  - Rich management UI for all entities: rejected to prevent scope creep.
  - No UI at all: rejected because operational debugging and validation become harder.

## Decision: Contract-first fake provider webhook endpoint

- Rationale: Needed to validate end-to-end pipeline before any real provider integration.
- Alternatives considered:
  - Use only seed/test data without endpoint: rejected because ingress contract would remain untested.
  - Integrate real provider APIs in V1: rejected by explicit scope boundaries.

## Decision: Pest test strategy focused on pipeline invariants

- Rationale: Ensures critical behaviors are covered with fast feedback loops and clear failure isolation.
- Alternatives considered:
  - Heavy browser-driven E2E first: rejected for slower iteration and brittle coverage.
  - Unit-only tests: rejected because pipeline integration behavior is central to feature value.
