---
name: using-laravel-entry-points
description: Implement or refactor Laravel controllers, commands, jobs, listeners, and scheduled tasks. Use for endpoints, authentication, request validation, simple reads, or extracting business orchestration into Actions.
---

# Laravel entry points

Keep controllers, commands, jobs, listeners, policies, and providers in Laravel's usual locations. Entry points authenticate, validate or normalize transport input, and translate application results or exceptions into transport output.

For meaningful business operations, use `Controller -> Action`, `Command -> Action`, `Job -> Action`, or `Listener -> Action`. Pass explicit input and an actor when authorization is required; the Action authorizes its use case. Background work must have an intentional actor or trusted system policy, not an accidental authorization bypass.

```php
public function destroy(Request $request, Order $order, CancelOrder $cancelOrder): Response
{
    $cancelOrder->handle($request->user(), $order->id);

    return response()->noContent();
}
```

Assume authentication middleware and imports for Laravel Request/Response, the domain Order, and the application CancelOrder. Route binding resolves the model; the Action still authorizes. Map domain exceptions centrally using Laravel's exception handling or an appropriate local adapter. Do not add HTTP status codes to domain exceptions.

For structured input, map validated request values into a Data object when useful. Domain invariants must also be enforced for non-HTTP callers.

## Keep simple operations simple

A simple list/detail read can use Eloquent directly, authorize with policies/gates, and return a Resource. Do not create an Action just to wrap a query or trivial framework operation. Move meaningful business behavior and coordination into Application; move invariants into Domain.

A listener may perform a small technical side effect directly. A multi-step business reaction should invoke an Action. External effects triggered by transactional changes must wait until commit.

Actions do not dispatch concrete `App\Jobs` classes. Prefer business events for asynchronous follow-up; reserve contracts for concrete infrastructure capabilities.
