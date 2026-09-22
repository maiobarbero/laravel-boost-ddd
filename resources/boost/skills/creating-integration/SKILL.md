---
name: creating-integration
description: Add an adapter for an external API or third-party system.
---

# Creating an External Integration

Place third-party technical integrations under:

App\Infrastructure\Integrations

Group integrations by provider or external system when useful.

Infrastructure classes may depend on vendor SDKs and framework-specific clients.

Keep external API concepts from leaking into the Domain.

Translate external responses into application or domain concepts at the integration boundary.

Introduce an interface only when dependency inversion, substitution, or testing provides a concrete benefit.

Do not create an interface automatically for every integration.
