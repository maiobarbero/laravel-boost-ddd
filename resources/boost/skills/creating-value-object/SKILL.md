---
name: creating-value-object
description: Create or change immutable business values with meaningful invariants, equality, or behavior, such as order numbers and money. Use when primitive values obscure domain rules; avoid wrapping every scalar.
---

# Value Objects

Use `App\Domain\<Capability>\ValueObjects` and the domain name, such as `OrderNumber` or `Money`, without a `ValueObject` suffix.

Value Objects are immutable by default and normally final. Validate invariants at construction, preserve them through operations, and compare by value when equality matters. Use specific domain exceptions for invalid values. Do not add HTTP validation or presentation dependencies.

For the order example, an `OrderNumber` is useful if it enforces a business format or supports meaningful operations. A numeric database ID alone does not justify a wrapper. A cancellation fee may justify Money with explicit currency and precision semantics; do not invent currency conversion rules or use floating-point arithmetic for exact money.

Reuse existing compatible value types and Eloquent casts. When persisting a Value Object, define and test its storage round trip only if the use case needs it. Avoid introducing a new package, generic Value Object base class, or custom casting framework automatically.

Application Data objects carry use-case input; Value Objects express business meaning. Choose based on responsibility rather than the number of properties.
