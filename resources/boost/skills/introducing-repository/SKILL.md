---
name: introducing-repository
description: Introduce a repository only when persistence needs a meaningful abstraction beyond direct Eloquent usage.
---

# Introducing a Repository

Direct Eloquent usage is the default.

Do not introduce repositories merely because the application uses DDD.

Introduce a repository only when it solves a concrete problem, such as:

- a meaningful persistence boundary
- multiple persistence implementations
- complex aggregate reconstruction
- persistence details that should be hidden from Domain/Application code

Do not create:

RepositoryInterface
EloquentRepository
Repository

as ceremony around simple Eloquent queries.

When direct Eloquent usage is clear and maintainable, prefer it.
