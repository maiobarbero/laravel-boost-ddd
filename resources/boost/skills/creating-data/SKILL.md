---
name: creating-data
description: Create or change structured input for application use cases, including extracting HTTP request data for Actions invoked from controllers, jobs, or commands. Avoid DTOs for trivial scalar input.
---

# Application input data

Use `App\Application\<Capability>\Data` and a `Data` suffix only when structured input improves the use case. Prefer immutable PHP objects; reuse an existing compatible Data library without adding a new dependency automatically.

Transport validation and normalization belong in FormRequests or other entry points. Data objects describe application input; business invariants remain in Domain and must also hold for calls from jobs or commands.

```php
namespace App\Application\Orders\Data;

final readonly class CancelOrderData
{
    public function __construct(
        public int $orderId,
        public string $reason,
    ) {}
}
```

This object is warranted if cancellation accepts a reason as well as an ID. If the use case only takes an order ID, pass the scalar directly. Keep the actor explicit and separate when that makes authorization clearer.

Map validated request values at the entry point. Do not accept FormRequests in Actions or add `fromRequest()` dependencies to Data classes. A typed Data object does not replace domain validation. Do not label a domain concept such as Money as application input merely because it carries data.
