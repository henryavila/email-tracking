<?php

namespace AppsInteligentes\EmailTracking;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use AppsInteligentes\EmailTracking\Commands\EmailTrackingCommand;

class EmailTrackingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('email-tracking')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_email-tracking_table')
            ->hasCommand(EmailTrackingCommand::class);
    }
}
