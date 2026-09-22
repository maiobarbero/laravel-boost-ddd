---
name: creating-domain-model
description: Create or extend an Eloquent model that represents a Domain concept.
---

# Creating a Domain Model

Place Domain Eloquent models under:

App\Domain\<Capability>\Models

Eloquent models may represent Domain entities directly.

Do not create a separate persistence model and domain entity unless there is a concrete need for that separation.

Put business behavior on the Domain model when the behavior naturally belongs to that entity.

Prefer:

$order->cancel();

over moving the cancellation rules into:

CancelOrder::handle()

The Action should orchestrate the use case; the Domain model should enforce its own business rules.

Do not add HTTP or presentation concerns to Domain models.
