<?php

declare(strict_types=1);

namespace MaioBarbero\LaravelBoostDdd\Tests;

use MaioBarbero\LaravelBoostDdd\LaravelBoostDddServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelBoostDddServiceProvider::class,
        ];
    }
}
