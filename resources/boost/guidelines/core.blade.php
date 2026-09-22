# Laravel-native DDD

This application follows a pragmatic, Laravel-native Domain-Driven Design architecture.

Laravel conventions should be preserved unless they conflict with clear domain or application boundaries.

## Architecture

Application code is organized into:

- `App\Domain`
- `App\Application`
- `App\Infrastructure`
- standard Laravel delivery mechanisms such as `Http`, `Console`, `Jobs`, and `Listeners`.

Organize Domain and Application code by business capability first.

Example:

App\Domain\Orders
App\Application\Orders

Avoid organizing business code globally only by technical type.

## Domain

The Domain layer contains business concepts, rules, and behavior.

Eloquent models may be Domain models.

Using Laravel inside the Domain is acceptable when it provides concrete value and does not introduce presentation or application concerns.

The Domain must not depend on Application, Infrastructure, HTTP, Console, Jobs, or other delivery mechanisms.

Prefer rich domain behavior over moving business rules into controllers or Actions.

## Application

The Application layer contains application use cases.

Use cases are called Actions.

Actions live under:

App\Application\<Capability>\Actions

Actions expose a public `handle()` method.

Actions should normally be final.

Application input objects live under:

App\Application\<Capability>\Data

and use the `Data` suffix.

Actions orchestrate Domain behavior but should not contain HTTP concerns.

## Infrastructure

Infrastructure contains concrete technical adapters such as:

- external integrations
- third-party APIs
- specialized persistence implementations

Do not move code into Infrastructure merely because it uses Laravel.

## Laravel delivery mechanisms

Controllers, Commands, Jobs, and Listeners are entry points or execution mechanisms.

Keep them thin.

They should translate input where necessary and delegate application behavior to Actions.

## Abstractions

Do not introduce architectural abstractions without a concrete reason.

Do not automatically create:

- repository interfaces
- repository implementations
- factories
- domain services
- DTOs
- adapters

Introduce them only when they solve an actual design problem.

## Verification

After changing application code:

1. Run the relevant behavioral tests.
2. Run the architecture test suite.

Architecture test failures must be fixed in the implementation.

Never remove, skip, ignore, or weaken an architecture test merely to make the suite pass unless the user explicitly requests an architectural change.
