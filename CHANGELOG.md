# Release Notes

## [v1.0.0](https://github.com/maiobarbero/laravel-boost-ddd/compare/v0.1.0...v1.0.0) - 2026-09-23

### Laravel Boost DDD v1.0.0

First stable release of Laravel Boost DDD.

Laravel Boost DDD teaches AI coding agents to structure Laravel applications using pragmatic, Laravel-native Domain-Driven Design while keeping Laravel conventions intact.

#### Highlights

* Laravel Boost guidelines for pragmatic Domain-Driven Design
* Focused agent skills for Actions, Data objects, Domain models, Value Objects, Domain Events, integrations, repositories, testing, and Laravel entry points
* Strict dependency boundaries with pragmatic implementation choices
* Direct Eloquent usage as the default
* Actions as application use cases with a consistent `handle()` API
* Incremental adoption without requiring legacy migrations
* Publishable Pest architecture tests for enforcing architectural invariants
* `boost-ddd:install` command for installing guidelines, skills, and architecture tests
* Laravel 12 and 13 support
* PHP 8.3, 8.4, and 8.5 support

#### Installation

```bash
composer require maiobarbero/laravel-boost-ddd --dev
php artisan boost-ddd:install

```
#### Philosophy

**Strict boundaries. Pragmatic implementation. Incremental adoption.**

Laravel conventions remain the default unless a DDD boundary gives a concrete reason to deviate.

## [v0.1.0](https://github.com/maiobarbero/laravel-boost-ddd/releases/tag/v0.1.0) - 2026-09-22

Initial pre-release.
