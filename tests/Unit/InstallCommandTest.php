<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use MaioBarbero\LaravelBoostDdd\LaravelBoostDddServiceProvider;

it('registers the install command', function () {
    $commands = array_keys(
        $this->app->make(Kernel::class)->all(),
    );

    expect($commands)
        ->toContain('boost-ddd:install');
});

it('publishes the architecture test', function () {
    $paths = LaravelBoostDddServiceProvider::pathsToPublish(
        LaravelBoostDddServiceProvider::class,
        'laravel-boost-ddd',
    );

    expect($paths)
        ->not->toBeEmpty()
        ->and(array_values($paths))
        ->toContain(base_path('tests/Architecture/DddArchitectureTest.php'));
});
