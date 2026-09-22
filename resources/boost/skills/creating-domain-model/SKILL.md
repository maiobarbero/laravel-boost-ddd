---
name: creating-domain-model
description: Create, extend, or fix Eloquent business models and their state transitions. Use when moving business rules out of controllers or Actions, or explicitly migrating a model into a DDD capability.
---

# Domain models

New adopted models live in `App\Domain\<Capability>\Models`. Eloquent entities are the default; do not split persistence models from domain entities without a concrete need. Extending an existing legacy model does not authorize relocating it.

Put behavior on the entity that owns its rules. Mutate state in the model; let the Action persist it:

```php
public function cancel(): void
{
    if ($this->status !== 'pending') {
        throw new OrderCannotBeCancelled();
    }

    $this->status = 'cancelled';
}
```

Here `OrderCannotBeCancelled` belongs to `App\Domain\Orders\Exceptions`. Use the project's existing enum/cast when applicable. No `save()`, HTTP response, `abort()`, policy invocation, or Infrastructure call belongs in this method. An Action calling it must explicitly save.

Domain may produce or record an event describing a transition; Application coordinates dispatch. Do not add an event collector or base aggregate solely for DDD.

Models may collaborate across capabilities in the same bounded context. Eloquent remains usable; repositories are not required for normal queries or persistence.

## Preserve Laravel behavior

When a model move is explicitly in scope:

- Keep factories in `database/factories`. Map the model to its factory with `UseFactory` or `newFactory()` when required, and map the factory back using its `$model` property or the installed version's equivalent.
- Keep policies in `app/Policies`; register them with `Gate::policy()` in a service provider when discovery no longer matches. Do not attach an outer policy dependency to a Domain model.
- Update and verify relationships, model imports, route binding, auth configuration if relevant, and persisted polymorphic type mappings if a stored class name changes.
- Verify factory creation, authorization, and affected behavior. Do not migrate unrelated models.

For the full sequence, see [order cancellation](../creating-action/references/order-cancellation.md).
