---
name: creating-domain-service
description: Create a Domain Service when domain behavior cannot naturally belong to one entity or value object.
---

# Creating a Domain Service

Create a Domain Service only when business behavior belongs to the Domain but cannot naturally be owned by a single Domain model or Value Object.

Place it under:

App\Domain\<Capability>\Services

Prefer behavior on Domain models whenever there is a clear owner.

Do not use Domain Services as generic containers for business logic.

Avoid generic classes such as:

OrderService
UserService
PaymentService

unless the class represents a cohesive domain concept.

Domain Services must not depend on HTTP or Application Actions.
