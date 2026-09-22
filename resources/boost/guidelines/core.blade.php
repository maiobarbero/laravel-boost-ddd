# Laravel-native DDD

Installing this package expresses an intent to adopt Domain-Driven Design. Apply these rules to relevant feature work, bug fixes, and refactoring even when the request does not mention DDD. Stay within the requested scope.

## Principles

- Strict boundaries.
- Pragmatic implementation.
- Incremental adoption.
- Laravel conventions remain the default unless a DDD boundary gives a concrete reason to deviate.

## Structure and adoption

Organize adopted business code by capability:

- `App\Domain\<Capability>`: business concepts, invariants, and behavior.
- `App\Application\<Capability>`: use cases and their input/contracts.
- `App\Infrastructure`: technical adapters and integrations.
- Standard Laravel directories: controllers, commands, jobs, listeners, policies, and providers.

New capabilities follow this structure. Preserve existing locations when modifying legacy code unless migration is part of the task. Entering `App\Domain` or `App\Application` means adopting the boundaries; architecture tests cover these namespaces automatically. Keep unadopted legacy outside them, without growing lists of test exceptions. Do not relocate unrelated code.

Migrated code accesses legacy business workflows through consumer-owned contracts implemented by Infrastructure adapters. Direct Eloquent access remains the persistence default, including existing models when appropriate; do not use it to bypass the legacy workflow boundary.

Capability folders are not automatically separate bounded contexts. Direct collaboration between capabilities in the same bounded context is allowed.

## Dependency boundaries

Domain must not depend on Application, Infrastructure, or delivery code. Application must not depend on Infrastructure or delivery code. These rules also apply to indirect access through service-location, string bindings, or custom facades.

An outer dependency requires a consumer-owned contract: `App\Application\<Capability>\Contracts` for an application need, or `App\Domain\<Capability>\Contracts` for a domain need. Infrastructure implements it; a Laravel service provider binds it. Inject the contract into the consumer.

Framework use is allowed within each layer's responsibility: Eloquent in Domain and Application, `DB` and `Gate` in Application. Keep HTTP clients and vendor SDKs in Infrastructure, and transport input/output in delivery code.

## Domain behavior and persistence

Eloquent models may be Domain entities under `App\Domain\<Capability>\Models`. A method such as `$order->cancel()` enforces invariants and changes state; the Action calls `save()`. Domain violations use specific domain exceptions, not HTTP responses, status codes, or `abort()`.

Domain may produce or record events. Application coordinates dispatch. Do not introduce an event collector or base aggregate without a concrete need.

## Application use cases

Actions live in `App\Application\<Capability>\Actions`, have verb-oriented names without an `Action` suffix, expose public `handle()`, and are normally final.

Actions load models, authorize the use case with an explicit actor using Laravel policies/gates, invoke domain behavior, persist changes, and coordinate events. Authentication belongs to entry points. Domain invariants must hold regardless of the entry point.

Use an Action-owned database transaction when multiple writes must succeed together. Do not wrap every operation automatically. Keep external API calls outside database transactions where possible; a database rollback cannot undo an external effect.

Actions may call other Actions for complete use cases, without fragmenting every step. Preserve authorization checks and let the outer Action coordinate the overall transaction.

Use immutable `App\Application\<Capability>\Data\*Data` objects for meaningful structured input, not every scalar. Reuse compatible existing libraries; simple PHP classes are the default. Never pass a FormRequest into an Action.

## Events and background work

Application coordinates event dispatch so external effects happen after commit. Use Laravel's after-commit event/listener mechanisms as appropriate; work required for atomic success stays explicit inside the transaction. A rollback must not trigger follow-up external effects.

Jobs and listeners are entry points: `Job -> Action`, `Listener -> Action` for meaningful use cases. For `Action -> async`, prefer events. Introduce a contract only for a real infrastructure capability, not automatically for scheduling. Small technical effects may remain in listeners.

## Laravel conventions

Entry points validate transport input, construct input data when useful, invoke Actions, and translate results/exceptions into transport output. Domain invariants are still checked in Domain. Simple reads may query Eloquent directly, with policies/gates for authorization and Resources for HTTP presentation; an Action is warranted when meaningful business behavior or coordination is involved.

Keep factories in `database/factories` and policies in `app/Policies`. When relocating a model, verify factory/model mappings, policy registration, relationships, route binding, and affected configuration/references. Register policies in a service provider rather than referencing delivery policies from Domain models.

Do not add repositories, domain construction factories, DTOs, services, or adapters without a concrete reason. This does not discourage normal Eloquent test factories. Direct Eloquent is the default; interfaces are necessary at actual outer dependency boundaries.

## Verification

Use Unit tests for pure behavior and Feature tests for framework/database behavior, including direct Action tests without HTTP. Prefer real domain behavior and database facilities; fake external boundaries. Cover relevant rejected invariants, authorization, rollback, and event timing as well as success.

Run relevant behavioral tests and the published architecture tests. If the stub has not been installed, report that verification gap. Static architecture checks do not prove business correctness or detect every dynamic dependency; review indirect access too.

Fix violations in adopted code. Never remove, skip, ignore, or weaken tests to make changes pass. Architectural changes require explicit user direction; unadopted legacy remains outside the checked namespaces.
