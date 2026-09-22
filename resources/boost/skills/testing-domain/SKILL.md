---
name: testing-domain
description: Test or fix domain rules, state transitions, value objects, and domain services directly. Use for business-rule regression tests, rejected transitions, and model behavior without HTTP.
---

# Testing Domain behavior

Test public behavior at the lowest useful level. Use Unit tests for pure PHP behavior. Use Feature tests with Laravel's TestCase when Eloquent behavior needs the framework or database; being a Domain test does not imply it is a pure unit test.

For `Order::cancel()`, verify that a pending order becomes cancelled in memory, a shipped order raises `OrderCannotBeCancelled`, and cancellation does not save itself. Persist an order in a database-backed test, invoke `cancel()`, and compare the in-memory status with a fresh query; persistence is the Action's responsibility.

If Domain produces events, assert the resulting fact and payload through its public API without requiring a particular internal event collection. Dispatch timing belongs in Application tests.

For Value Objects, test invalid construction and meaningful value behavior. For domain services, test business outcomes with real domain objects and substitute only external contracts when needed. Avoid testing private methods or mirroring implementation details.

Keep Eloquent factories in `database/factories`, with explicit mappings for relocated models when needed. Prefer factories and Laravel database facilities over mocked Eloquent behavior.

See [order cancellation](../creating-action/references/order-cancellation.md) for a rejected-transition test and the surrounding application flow. Run architecture tests when changing adopted code.
