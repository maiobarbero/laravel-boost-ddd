---
name: introducing-repository
description: Evaluate, introduce, or refactor persistence abstractions when direct Eloquent is insufficient. Use for repository requests, complex aggregate reconstruction, or multiple persistence implementations; reject CRUD wrappers without a concrete benefit.
---

# Repositories only when needed

Direct Eloquent is the default in Domain and Application. Loading an order and calling `$order->save()` from `CancelOrder` does not require a repository, interface, or separate persistence entity.

Introduce a repository only for an actual persistence problem, such as complex aggregate reconstruction, multiple implementations, or a meaningful boundary hiding specialized storage.

When the implementation lives in Infrastructure, put the contract with its consumer in `App\Domain\<Capability>\Contracts` or `App\Application\<Capability>\Contracts`. Infrastructure implements it; a Laravel service provider binds it. The consumer receives the contract rather than the concrete implementation.

Name the contract for the capability it provides and use consumer-owned types in its API. Avoid generic `RepositoryInterface`, `BaseRepository`, or CRUD forwarding methods around Eloquent. Do not introduce a repository solely to mock a model in an Action test.

Keep transaction coordination in the Action. A repository should not silently commit the caller's unit of work. Reusing an existing suitable abstraction is preferable to building a parallel one.
