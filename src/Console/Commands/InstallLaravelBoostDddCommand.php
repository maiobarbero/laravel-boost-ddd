<?php

declare(strict_types=1);

namespace MaioBarbero\LaravelBoostDdd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Pest\TestSuite;

class InstallLaravelBoostDddCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'boost-ddd:install';

    /**
     * The command description.
     */
    protected $description = 'Install Laravel Boost DDD Skills and guidelines for the project';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! class_exists(TestSuite::class)) {
            $this->components->error(
                'Pest is required to use the architecture tests.',
            );

            $this->components->info(
                'Install it with: composer require pestphp/pest --dev',
            );

            return self::FAILURE;
        }

        $this->info('Installing Laravel Boost DDD');

        $this->components->task(
            'Publishing architecture tests',
            fn () => Artisan::call('vendor:publish', [
                '--tag' => 'laravel-boost-ddd',
                '--force' => false,
            ]) === self::SUCCESS,
        );

        $this->components->task(
            'Discovering Laravel Boost resources',
            fn () => Artisan::call('boost:update', [
                '--discover' => true,
            ]) === self::SUCCESS,
        );

        $this->newLine();

        $this->components->info('Laravel Boost DDD installed.');

        return self::SUCCESS;
    }
}
