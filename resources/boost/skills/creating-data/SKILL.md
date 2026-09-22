---
name: creating-data
description: Create an Application Data object used to pass structured input into an Action.
---

# Creating Application Data

Use a Data object when an Action needs structured input that should not depend on HTTP.

Place it under:

App\Application\<Capability>\Data

Use the `Data` suffix.

Example:

PlaceOrderData
RegisterCustomerData

Data objects represent application input, not Domain concepts.

Do not pass FormRequest instances into Actions.

Prefer immutable Data objects.

Do not create a Data object for trivial scalar input unless it improves clarity.
