<?php

namespace MaioBarbero\LaravelBoostDdd;

use Illuminate\Support\ServiceProvider;

final class LaravelBoostDddServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../stubs/tests/Architecture/DddArchitectureTest.php' => base_path('tests/Architecture/DddArchitectureTest.php'),
        ], 'laravel-boost-ddd');
    }
}
