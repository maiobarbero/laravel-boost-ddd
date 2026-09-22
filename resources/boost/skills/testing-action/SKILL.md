---
name: testing-action
description: Test an Application Action as an application use case.
---

# Testing an Action

Test Actions through their public `handle()` method.

Verify the observable outcome of the use case.

Mock external boundaries only when appropriate.

Do not mock Domain models merely to isolate the Action from the Domain.

Prefer real Domain behavior.

HTTP behavior belongs in Feature tests for the HTTP adapter, not in Action tests.
