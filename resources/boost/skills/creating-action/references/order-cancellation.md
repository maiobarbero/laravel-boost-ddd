# Order cancellation: one connected example

Use this example to understand the boundaries, not as a mandatory scaffold. Assume integer model keys, an authenticated user, and these existing tables:

- `orders`: `id`, `user_id`, `status`, timestamps.
- `order_cancellations`: `id`, `order_id`, `actor_id`, timestamps.

The audit record is a business requirement in this example, which makes the two writes transactional. Do not add audit tables to every use case. Relationships, migrations, and factory attributes should follow the consuming application.

## Domain behavior

```php
// app/Domain/Orders/Exceptions/OrderCannotBeCancelled.php
namespace App\Domain\Orders\Exceptions;

use DomainException;

final class OrderCannotBeCancelled extends DomainException {}
```

```php
// app/Domain/Orders/Models/Order.php
namespace App\Domain\Orders\Models;

use App\Domain\Orders\Exceptions\OrderCannotBeCancelled;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(OrderFactory::class)]
class Order extends Model
{
    use HasFactory;

    public function cancel(): void
    {
        if ($this->status !== 'pending') {
            throw new OrderCannotBeCancelled('Only pending orders can be cancelled.');
        }

        $this->status = 'cancelled';
    }
}
```

In `Database\Factories\OrderFactory`, set `protected $model = Order::class` with the domain model import. Keep the factory in `database/factories`; supply normal defaults such as a pending status and a user. Use an existing status enum/cast instead of these strings when the application has one.

```php
// app/Domain/Orders/Models/OrderCancellation.php
namespace App\Domain\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    protected $fillable = ['order_id', 'actor_id'];
}
```

## Authorization stays Laravel-native

```php
// app/Policies/OrderPolicy.php
namespace App\Policies;

use App\Domain\Orders\Models\Order;
use Illuminate\Contracts\Auth\Authenticatable;

final class OrderPolicy
{
    public function cancel(Authenticatable $actor, Order $order): bool
    {
        return (string) $actor->getAuthIdentifier() === (string) $order->user_id;
    }
}
```

Register this mapping in a normal service provider's `boot()` method:

```php
\Illuminate\Support\Facades\Gate::policy(
    \App\Domain\Orders\Models\Order::class,
    \App\Policies\OrderPolicy::class,
);
```

The policy answers who may cancel; the model determines whether its state allows cancellation. The Domain model does not reference its policy.

## Fact and application orchestration

```php
// app/Domain/Orders/Events/OrderCancelled.php
namespace App\Domain\Orders\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

final readonly class OrderCancelled implements ShouldDispatchAfterCommit
{
    public function __construct(public int $orderId) {}
}
```

```php
// app/Application/Orders/Actions/CancelOrder.php
namespace App\Application\Orders\Actions;

use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderCancellation;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CancelOrder
{
    public function handle(Authenticatable $actor, int $orderId): Order
    {
        return DB::transaction(function () use ($actor, $orderId): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);

            Gate::forUser($actor)->authorize('cancel', $order);

            $order->cancel();
            $order->save();

            OrderCancellation::query()->create([
                'order_id' => $order->getKey(),
                'actor_id' => $actor->getAuthIdentifier(),
            ]);

            event(new OrderCancelled((int) $order->getKey()));

            return $order;
        });
    }
}
```

The lock protects this read/check/write sequence where the database supports row locks; adapt concurrency control to the actual storage engine. Both writes use the same connection. No DTO is needed for one order ID, and no repository is needed for ordinary Eloquent persistence.

Here the Action constructs the event. Domain may instead return or record the event; Application still coordinates dispatch. Laravel defers this event until the outer transaction commits. An outer Action can therefore call this use case within a larger transaction without publishing a fact about rolled-back work.

For a cancellation that requires a remote refund, a queued listener can invoke a refund Action after commit. That Action depends on `App\Application\Orders\Contracts\PaymentGateway`; an Infrastructure adapter implements it. The cancellation transaction does not make a remote refund atomic. Define retries, deduplication, and any refund state according to that business requirement.

## Behavioral tests

Use `tests/Feature/Orders` with the application's TestCase and `RefreshDatabase` for ordinary model/Action tests. The following snippets assume the imports shown, working factories, and the policy registration above:

```php
use App\Application\Orders\Actions\CancelOrder;
use App\Domain\Orders\Exceptions\OrderCannotBeCancelled;
use App\Domain\Orders\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

it('changes domain state without persisting it', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    $order->cancel();

    expect($order->status)->toBe('cancelled');
    expect($order->fresh()->status)->toBe('pending');
});

it('persists an authorized cancellation and its audit record', function () {
    $actor = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $actor->id, 'status' => 'pending']);

    app(CancelOrder::class)->handle($actor, $order->id);

    expect($order->fresh()->status)->toBe('cancelled');
    $this->assertDatabaseHas('order_cancellations', [
        'order_id' => $order->id,
        'actor_id' => $actor->id,
    ]);
});

it('rejects an unauthorized actor without writing a cancellation', function () {
    $order = Order::factory()->create(['status' => 'pending']);
    $otherActor = User::factory()->create();

    expect(fn () => app(CancelOrder::class)->handle($otherActor, $order->id))
        ->toThrow(AuthorizationException::class);

    expect($order->fresh()->status)->toBe('pending');
    $this->assertDatabaseMissing('order_cancellations', ['order_id' => $order->id]);
});

it('rejects cancellation of a shipped order', function () {
    $actor = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $actor->id, 'status' => 'shipped']);

    expect(fn () => app(CancelOrder::class)->handle($actor, $order->id))
        ->toThrow(OrderCannotBeCancelled::class);

    expect($order->fresh()->status)->toBe('shipped');
    $this->assertDatabaseMissing('order_cancellations', ['order_id' => $order->id]);
});
```

`App\Models\User` is intentionally left in its existing Laravel location. This example does not require migrating authentication to a new capability.

## Commit and rollback tests

Run these in a separate Feature test file using the application's TestCase and `DatabaseMigrations`, **without** an enclosing `RefreshDatabase`/`DatabaseTransactions` transaction inherited from `tests/Pest.php`. Keep the real event dispatcher and use a recording listener; fake or disable actual external boundaries in the consuming application.

```php
use App\Application\Orders\Actions\CancelOrder;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

it('delivers the cancellation fact only after the outer commit', function () {
    $actor = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $actor->id, 'status' => 'pending']);
    $received = [];

    Event::listen(OrderCancelled::class, function (OrderCancelled $event) use (&$received) {
        $received[] = $event->orderId;
    });

    DB::transaction(function () use ($actor, $order, &$received) {
        app(CancelOrder::class)->handle($actor, $order->id);
        expect($received)->toBe([]);
    });

    expect($received)->toBe([$order->id]);
});

it('rolls back the writes and discards the fact when the outer use case fails', function () {
    $actor = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $actor->id, 'status' => 'pending']);
    $received = [];

    Event::listen(OrderCancelled::class, function (OrderCancelled $event) use (&$received) {
        $received[] = $event->orderId;
    });

    expect(fn () => DB::transaction(function () use ($actor, $order) {
        app(CancelOrder::class)->handle($actor, $order->id);
        throw new RuntimeException('The outer use case failed.');
    }))->toThrow(RuntimeException::class, 'The outer use case failed.');

    expect($order->fresh()->status)->toBe('pending');
    $this->assertDatabaseMissing('order_cancellations', ['order_id' => $order->id]);
    expect($received)->toBe([]);
});
```

Also test the Action's own failure path between coordinated writes when relevant. HTTP middleware, validation, and exception rendering need separate entry-point tests. No static architecture test can prove these behaviors.

Laravel reference: [events after database transactions](https://laravel.com/docs/13.x/events#dispatching-events-after-database-transactions), [factory discovery](https://laravel.com/docs/13.x/eloquent-factories#model-and-factory-discovery-conventions).
