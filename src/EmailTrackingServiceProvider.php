<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasRoute('webhooks')
            ->discoversMigrations();
    }
}
