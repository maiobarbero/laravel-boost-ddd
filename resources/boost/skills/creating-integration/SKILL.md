---
name: creating-integration
description: Add, change, or debug external API integrations, vendor SDK adapters, and bridges to legacy business workflows. Use when an Action or domain service needs an outer technical dependency.
---

# External and legacy integrations

Put concrete external adapters in `App\Infrastructure\Integrations`, grouped by provider when useful. Legacy workflow bridges may live in `App\Infrastructure\Legacy`. Keep HTTP clients, SDK objects, transport failures, and provider-specific payloads at this boundary; translate them into consumer concepts.

## Connect the consumer

A consumer in Domain or Application cannot depend on a concrete Infrastructure class. This boundary is a concrete reason for a contract:

- Application need: `App\Application\<Capability>\Contracts`.
- Domain need: `App\Domain\<Capability>\Contracts`.
- Implementation: Infrastructure.
- Binding: a standard Laravel service provider.

```php
// App\Application\Orders\Contracts\PaymentGateway
interface PaymentGateway
{
    public function refund(string $paymentReference, string $idempotencyKey): void;
}

// In a service provider's register() method:
$this->app->bind(
    \App\Application\Orders\Contracts\PaymentGateway::class,
    \App\Infrastructure\Integrations\Payments\ProviderPaymentGateway::class,
);
```

After `OrderCancelled` commits, a queued listener can invoke a refund Action that receives `PaymentGateway`. The adapter implements that interface and translates the provider response. Derive a stable idempotency key for a refund operation and use the provider's supported mechanism or an application deduplication strategy.

Do not inject SDK types into the contract or resolve concrete adapters from Actions through `app()`, strings, or custom facades. Do not create an interface for every helper inside Infrastructure.

Keep external calls outside database transactions where possible. A local rollback cannot reverse a remote refund; design retries and failure handling for the actual use case rather than treating the database transaction as distributed atomicity.

For migrated code using legacy business workflows, the same contract/adapter pattern applies. Direct Eloquent persistence is still allowed; it must not become a shortcut into legacy workflow orchestration.

Use events by default for asynchronous reactions. A scheduling contract is warranted only for a real infrastructure capability, not merely because a Job exists.
