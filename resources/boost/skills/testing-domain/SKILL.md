---
name: testing-domain
description: Test Domain behavior and business rules directly.
---

# Testing Domain Behavior

Test Domain rules at the lowest useful level.

Prefer testing behavior through public Domain APIs.

Example:

$order->cancel();

Then assert the resulting Domain state or event.

Do not test implementation details.

Domain tests should not require HTTP requests when the behavior can be tested directly.

Use Laravel database facilities when the Domain model is an Eloquent model and persistence is relevant to the behavior.
