---
name: creating-action
description: Create an Application Action representing one application use case.
---

# Creating an Action

Use an Action for one application use case.

Place it under:

App\Application\<Capability>\Actions

Use a verb-oriented name describing the operation:

- PlaceOrder
- CancelOrder
- RegisterCustomer

Do not add the `Action` suffix.

Expose the use case through:

public function handle(...)

Actions should normally be final.

An Action may:

- load Domain models
- invoke Domain behavior
- coordinate multiple domain objects
- manage application-level orchestration

An Action must not:

- accept FormRequest objects
- return HTTP responses or Resources
- contain presentation logic

Keep business rules in the Domain when they belong to the business rather than to application orchestration.
