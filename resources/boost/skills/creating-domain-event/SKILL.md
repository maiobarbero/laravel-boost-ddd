---
name: creating-domain-event
description: Create a Domain Event representing a meaningful business fact that has occurred.
---

# Creating a Domain Event

Place Domain Events under:

App\Domain\<Capability>\Events

Name events as facts that already happened.

Prefer:

OrderPlaced
SubscriptionCancelled
CustomerRegistered

Domain Events describe business facts.

They must not contain presentation behavior or execute side effects themselves.

React to Domain Events through listeners when another part of the application needs to perform follow-up work.

Do not create events for implementation details that have no domain meaning.
