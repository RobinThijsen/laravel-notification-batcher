<?php

namespace RobinThijsen\NotificationBatcher;

use RobinThijsen\NotificationBatcher\Commands\MakeBatchClass;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationBatcherServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-notification-batcher')
            ->hasConfigFile()
            ->hasCommand(MakeBatchClass::class)
            ->hasMigrations('create_notification_batchers_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });
    }

    public function registeringPackage()
    {
        $this->app->singleton(Batcher::class, fn () => new Batcher());
        $this->app->alias(Batcher::class, 'batcher');
    }
}
