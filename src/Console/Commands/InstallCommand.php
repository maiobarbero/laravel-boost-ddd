<?php

declare(strict_types=1);

namespace MaioBarbero\LaravelBoostDdd\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Boost\Support\Config;
use Pest\TestSuite;

class InstallCommand extends Command
{
    private const string PACKAGE = 'maiobarbero/laravel-boost-ddd';

    protected $signature = 'boost-ddd:install';

    protected $description = 'Install Laravel Boost DDD skills, guidelines and architectural tests';

    public function handle(Config $config): int
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

        $packages = array_unique([
            ...$config->getPackages(),
            self::PACKAGE,
        ]);

        $config->setPackages($packages);

        $boostOptions = [
            '--guidelines' => true,
            '--skills' => true,
        ];

        // Existing Boost installations do not need to be prompted again.
        if ($config->getAgents() !== []) {
            $boostOptions['--no-interaction'] = true;
        }

        if ($this->call('boost:install', $boostOptions) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($this->call('vendor:publish', [
            '--tag' => 'laravel-boost-ddd',
        ]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->components->info('Laravel Boost DDD installed.');

        return self::SUCCESS;
    }
}
