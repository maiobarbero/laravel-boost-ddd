---
name: using-laravel-entry-points
description: Use Laravel controllers, commands, jobs, listeners, and other entry points without leaking application or domain logic into them.
---

# Using Laravel Entry Points

Laravel entry points receive external stimuli and connect them to the application.

Common entry points include:

- Controllers
- Console Commands
- Jobs
- Listeners
- Scheduled tasks

Keep entry points thin.

When an entry point triggers meaningful application behavior, delegate that
behavior to an Application Action.

Prefer:

Controller -> Action
Command -> Action
Job -> Action
Listener -> Action

Entry points may:

- receive framework-specific input
- validate or normalize transport-specific input
- create Application Data objects
- invoke Actions
- transform Action results into framework-specific output
- perform small, local technical side effects

Entry points must not:

- implement business rules
- become the primary implementation of a use case
- contain complex application orchestration

Do not create an Action merely to wrap a trivial framework-specific operation.

If behavior represents a business rule, move it to the Domain.

If behavior represents an application use case or meaningful orchestration,
move it to an Application Action.

If behavior is a small technical side effect with no meaningful application
semantics, it may remain in the entry point.
