---
name: creating-domain-event
description: Create, modify, or debug business events and their follow-up reactions, including order cancellation events, queued listeners, transaction timing, and rollback behavior.
---

# Domain events

Put business facts under `App\Domain\<Capability>\Events` and name them in the past tense, such as `OrderCancelled`. Do not create domain events for implementation details.

Domain may produce or record events; Application coordinates their dispatch. A direct dispatch call in an Action is a simple option, not the only permitted production mechanism. Do not require an event collector or aggregate base class.

Events carry facts and do not execute side effects or contain presentation logic. Prefer immutable scalar IDs or snapshots when a reaction needs the fact as it happened; a reloaded Eloquent model can reflect later state.

## Transaction timing

External reactions must happen after commit. For an event whose reactions should all wait, use Laravel's `ShouldDispatchAfterCommit`:

```php
namespace App\Domain\Orders\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

final readonly class OrderCancelled implements ShouldDispatchAfterCommit
{
    public function __construct(public int $orderId) {}
}
```

Application can call `event(new OrderCancelled($order->id))` after saving, inside its transaction. Laravel defers this event until commit and discards it on rollback; without a transaction it dispatches immediately.

If an existing event must dispatch earlier, defer the external listener/job using the installed Laravel version's after-commit mechanism instead. Work required for atomic success should be explicit in the Action's transaction rather than hidden in follow-up listeners.

Keep listeners in Laravel's usual location. They delegate meaningful application behavior to Actions; a small technical side effect may stay in the listener. Prefer this event path for asynchronous follow-up work. Account for retries and duplicate delivery when the reaction changes an external system; after-commit dispatch alone does not guarantee delivery or exactly-once execution.

See [order cancellation](../creating-action/references/order-cancellation.md) for the connected dispatch example. Test successful commit and rollback separately; an event fake alone does not verify real listener timing.
