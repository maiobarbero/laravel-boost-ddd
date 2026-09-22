---
name: creating-value-object
description: Create a Domain Value Object for a meaningful immutable business concept.
---

# Creating a Value Object

Create a Value Object only when a value has meaningful domain behavior, validation, or semantics.

Place it under:

App\Domain\<Capability>\ValueObjects

Use the domain concept as its name.

Prefer:

Money
EmailAddress
OrderNumber

Avoid:

MoneyValueObject
EmailValueObject

Value Objects should normally be immutable and final.

Validate invariants at construction.

Do not create Value Objects merely to wrap every scalar value.
