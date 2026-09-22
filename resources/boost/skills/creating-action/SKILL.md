---
name: creating-action
description: Create, modify, or refactor application use cases into Laravel-native DDD Actions. Use for business operations such as placing or cancelling orders, extracting controller orchestration, and fixing use-case behavior.
---

# Creating and changing Actions

Use `App\Application\<Capability>\Actions`, a verb-oriented name such as `CancelOrder` (no `Action` suffix), and a public `handle()` method. Actions are normally final. Preserve legacy locations unless migration is in scope.

## Responsibilities

1. Accept explicit input and an actor when authorization is required; never a FormRequest or an implicit HTTP user.
2. Load the relevant models and authorize the use case with `Gate::forUser($actor)` and Laravel policies. Authentication stays in the entry point.
3. Invoke domain behavior, then persist: `$order->cancel(); $order->save();`.
4. Own a transaction when coordinated writes require atomicity. Keep external calls outside it where possible.
5. Coordinate dispatch of events, whether produced by Domain or constructed by Application, ensuring external reactions happen after commit.

Domain invariants and domain exceptions belong to Domain; orchestration belongs here. Return models, values, or application results, never HTTP responses or Resources. Use a Data object only for meaningful structured input.

Inject consumer-owned contracts for Infrastructure dependencies. Bind implementations in a service provider; do not resolve concrete adapters through `app()`, strings, or custom facades. Direct Eloquent, `DB`, and `Gate` remain available within these responsibilities.

For follow-up asynchronous work, prefer events. Do not reference `App\Jobs` from an Action. A contract is justified only when it represents a real infrastructure capability.

Actions may invoke another complete use case, retaining its authorization checks. Avoid chains of tiny Actions; the outer Action owns transaction coordination. Models from another capability may be used directly within the same bounded context.

Simple authorized reads may remain in an entry point. Do not introduce an Action for every query or trivial framework operation.

## Connected example

Read [the order cancellation example](references/order-cancellation.md) when implementing or reviewing the relationship between authorization, model behavior, persistence, events, and tests. It demonstrates a transaction for an order update plus an audit record, without requiring a DTO, repository, or event collector.
