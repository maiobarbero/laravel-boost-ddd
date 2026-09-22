# Laravel Boost DDD

<p align="center">
  <a href="https://packagist.org/packages/maiobarbero/laravel-boost-ddd"><img src="https://img.shields.io/packagist/v/maiobarbero/laravel-boost-ddd?style=flat" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/maiobarbero/laravel-boost-ddd"><img src="https://badge.laravel.cloud/php-badge/maiobarbero/laravel-boost-ddd?style=flat" alt="PHP Compatibility"></a>
  <a href="https://packagist.org/packages/maiobarbero/laravel-boost-ddd"><img src="https://badge.laravel.cloud/badge/maiobarbero/laravel-boost-ddd?style=flat" alt="Laravel 13"></a>
  <a href="https://github.com/laravel/boost"><img src="https://badge.laravel.cloud/boost-badge.svg?style=flat" alt="Laravel Boost"></a>
</p>

Laravel Boost guidelines, agent skills, and publishable Pest architecture tests for pragmatic, Laravel-native Domain-Driven Design.

Installing this package expresses an intent to adopt DDD. Its guidance applies to new features, bug fixes, and refactoring, even when the prompt does not explicitly mention architecture. It does not migrate an application automatically.

## Philosophy

**Strict boundaries. Pragmatic implementation. Incremental adoption.**

**Laravel conventions remain the default unless a DDD boundary gives a concrete reason to deviate.**

- **Strict boundaries:** Domain knows neither Application nor Infrastructure nor delivery code. Application does not depend on Infrastructure or delivery code. Consumer-owned contracts connect inner layers to outer implementations through Laravel service-provider bindings. Container lookups and custom facades must not bypass these boundaries.
- **Pragmatic implementation:** Eloquent models can be domain entities. Direct Eloquent persistence is the default. Repositories, Data objects, services, and other abstractions need a concrete purpose. Capability folders are not automatically separate bounded contexts.
- **Incremental adoption:** new capabilities use the DDD structure. Legacy stays in place until migration is explicitly in scope. Adopted code accesses legacy business workflows through contracts and Infrastructure adapters. Architecture tests cover adopted namespaces automatically, without growing lists of exceptions.
- **Laravel conventions:** keep controllers, commands, jobs, listeners, policies, providers, migrations, and test factories in their usual locations. Reuse Laravel's container, policies, events, queues, and testing facilities.

## Responsibilities

| Area                                   | Responsibility                                                                                                 |
| -------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| `App\Domain\<Capability>`              | Business behavior, invariants, value objects, domain exceptions, and business facts.                           |
| `App\Application\<Capability>\Actions` | Use-case authorization, orchestration, persistence, transaction coordination, and event dispatch coordination. |
| `App\Application\<Capability>\Data`    | Meaningful structured input, independent of HTTP; unnecessary for trivial scalar input.                        |
| Consumer's `Contracts` directory       | A Domain or Application interface for an actual outer dependency.                                              |
| `App\Infrastructure`                   | External integrations, specialized persistence, and legacy workflow adapters.                                  |
| Laravel entry points                   | Authentication, transport validation, invoking use cases, and translating output/errors.                       |

An order's `cancel()` method enforces its rules and changes state; `CancelOrder::handle()` authorizes and calls `save()`. An Action owns a database transaction when coordinated writes must succeed together. Domain may produce or record events; Application coordinates dispatch so external effects happen after commit. An event collection mechanism is not mandatory.

Jobs invoke Actions. Actions prefer events for asynchronous follow-up; a contract is appropriate only for a real infrastructure capability. Simple authorized reads may query Eloquent directly from an entry point. Actions may reuse other complete use cases without becoming chains of tiny wrappers.

See the [connected order cancellation example](resources/boost/skills/creating-action/references/order-cancellation.md) for authorization, model behavior, persistence, events, and success/failure tests.

## Installation

```sh
composer require maiobarbero/laravel-boost-ddd
```

### Enable the guidance and skills

```sh
php artisan boost:install
```

Enable **guidelines** and **skills**, select `maiobarbero/laravel-boost-ddd` in the third-party package selection, and choose your AI agents. Configure Boost's MCP integration too if you want its tools and have not already configured them.

Composer installation alone does not activate the guidance. Boost discovers the package's `resources/boost/guidelines` and `resources/boost/skills` directories, then installs the selected content for your agents. Reopen the agent session if needed to pick up installed skills. See [Laravel Boost](https://github.com/laravel/boost) for its installation and agent configuration.

### Publish and run architecture tests

```sh
php artisan vendor:publish --tag=laravel-boost-ddd
vendor/bin/pest tests/Architecture
```

The provider publishes `tests/Architecture/DddArchitectureTest.php`. Add the directory to your application's `phpunit.xml` under `<testsuites>` if its existing suite configuration only includes Unit and Feature:

```xml
<testsuite name="Architecture">
    <directory>tests/Architecture</directory>
</testsuite>
```

The adoption boundary is the code's location: everything under `App\Domain` and `App\Application` is covered. Legacy elsewhere stays outside the target scope. Rules for absent layers/directories are skipped; that is expected before adoption starts. Do not move legacy into these namespaces and then add exclusions to silence failures.

The stub checks forbidden dependencies, transport dependencies, Action method presence/naming, and Data suffixes. It does not prove authorization, atomicity, domain correctness, public method visibility, or every dynamic dependency. Behavioral tests and review remain necessary.

### Updates

```sh
composer update maiobarbero/laravel-boost-ddd
php artisan boost:update
```

Review regenerated guidance and skills. Published architecture tests are application-owned files: compare your copy with `vendor/maiobarbero/laravel-boost-ddd/stubs/tests/Architecture/DddArchitectureTest.php` and merge changes deliberately. Do not use `vendor:publish --force` to overwrite local changes blindly.

## Installation for LLM agents

Use these instructions when a user asks you to install this package in a Laravel application:

1. Inspect the application's instructions, Composer requirements, Pest setup, and existing Boost configuration. Confirm dependency compatibility; preserve unrelated changes. Use the installation command above.
2. For an existing Boost setup, merge `maiobarbero/laravel-boost-ddd` into the `packages` array in `boost.json`, preserving other packages, agents, and settings. For a new setup, use the intended agent's supported Boost identifier; infer it from the environment when clear, otherwise ask which agent to configure. Set that agent in `agents`. The fragment below illustrates the relevant keys for Codex; it is not a replacement for an existing file.

   ```json
   {
     "agents": ["codex"],
     "packages": ["maiobarbero/laravel-boost-ddd"]
   }
   ```

3. Install both guidance and skills without interactive prompts:

   ```sh
   php artisan boost:install --guidelines --skills --no-interaction
   ```

   Preselecting the package matters: a non-interactive installer does not automatically opt into a newly discovered third-party package. These flags install guidance and skills; configure MCP separately if requested and missing. Check the installed Boost version's command help/configuration if its interface differs.

4. Publish the architecture test with `php artisan vendor:publish --tag=laravel-boost-ddd`. Preserve any existing copy and merge applicable rule changes. Ensure Pest is initialized and the Architecture directory is included in the normal test run.
5. Verify that generated agent instructions contain the DDD guidelines, the agent can access all 11 skills and the order example, and `vendor/bin/pest tests/Architecture` succeeds or reports only expected skips for absent adopted layers. Run relevant existing behavioral tests. Report actual results and any verification gaps.
6. Treat installation as intent to adopt DDD, not permission to migrate the entire application. Apply the rules to subsequent relevant work within its scope. Keep legacy outside adopted namespaces until explicitly migrated; never create a growing test-exception list.

## Included skills

| Skill                        | Purpose                                                                          |
| ---------------------------- | -------------------------------------------------------------------------------- |
| `creating-action`            | Implement and refactor use cases, authorization, persistence, and orchestration. |
| `creating-data`              | Introduce structured application input when useful.                              |
| `creating-domain-model`      | Keep business transitions and invariants on Eloquent entities.                   |
| `creating-value-object`      | Model meaningful immutable business values.                                      |
| `creating-domain-service`    | Place cohesive rules that have no natural entity/value owner.                    |
| `creating-domain-event`      | Express business facts and coordinate safe reaction timing.                      |
| `creating-integration`       | Connect consumer-owned contracts to external or legacy adapters.                 |
| `introducing-repository`     | Introduce persistence abstractions only for an actual need.                      |
| `using-laravel-entry-points` | Keep delivery code thin while retaining Laravel conventions.                     |
| `testing-domain`             | Verify invariants and state transitions directly.                                |
| `testing-action`             | Verify use-case outcomes, authorization, rollback, and event timing.             |

Skills are selected by the agent for relevant tasks; they are guidance, not a guarantee of automatic activation. The always-loaded core guidelines establish the shared boundaries.

## License

[MIT](LICENSE)
