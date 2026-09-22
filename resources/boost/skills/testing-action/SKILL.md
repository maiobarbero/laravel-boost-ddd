---
name: testing-action
description: Test or debug application use cases through their public Action API, including persistence, authorization failures, atomic rollback, and post-commit reactions. Use when adding regression tests for business operations.
---

# Testing Actions

Test public `handle()` behavior, preferably with real domain models and database facilities. Action tests requiring Laravel or a database belong in Feature tests even when no HTTP request occurs. Use the application's TestCase and appropriate database isolation; do not mock Domain models merely to isolate an Action.

For the shared `CancelOrder` example, cover:

- An authorized actor cancels a pending order; the persisted state changes and an audit record is written.
- An unauthorized actor receives an authorization exception; state and audit records remain unchanged.
- A shipped order raises `OrderCannotBeCancelled`; no cancellation is persisted.
- A failure after the order save rolls back both coordinated writes and causes no external reaction.

Fake consumer-owned external contracts or use Laravel's HTTP/Mail/Queue fakes at the relevant boundary. An adapter's HTTP fake tests request/response translation; an Action's contract substitute tests the use case.

## Event timing

Separate dispatch assertions from timing tests. `Event::fake()` can prove dispatch intent; it does not prove a real listener waited for commit. Test the real dispatcher with a harmless recording listener: no reaction inside the transaction, one after successful commit, none after rollback.

Do not rely on a test-wide transaction to commit. For actual after-commit tests, use a database isolation strategy without an enclosing transaction (for example `DatabaseMigrations`) and explicitly exercise commit/rollback. Fake only the external boundary so no real external effect occurs.

HTTP validation, authentication middleware, exception rendering, and Resources belong in separate entry-point Feature tests. Run the architecture suite for adopted code too.

Read [order cancellation](../creating-action/references/order-cancellation.md) for the implementation and connected test examples.
