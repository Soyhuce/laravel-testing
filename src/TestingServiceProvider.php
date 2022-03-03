<?php declare(strict_types=1);

namespace Soyhuce\Testing;

use Soyhuce\Testing\Commands\TestingCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TestingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-testing')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-testing_table')
            ->hasCommand(TestingCommand::class);
    }
}
