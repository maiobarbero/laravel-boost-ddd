<?php

namespace MaioBarbero\LaravelBoostDdd;

use Illuminate\Support\ServiceProvider;
use MaioBarbero\LaravelBoostDdd\Console\Commands\InstallLaravelBoostDddCommand;

final class LaravelBoostDddServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/tests/Architecture/DddArchitectureTest.php' => base_path('tests/Architecture/DddArchitectureTest.php'),
            ], 'laravel-boost-ddd');

            $this->commands([
                InstallLaravelBoostDddCommand::class,
            ]);
        }
    }
}
