<?php

namespace AppsInteligentes\EmailTracking;

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
            ->hasTranslations()
            ->hasRoute('webhooks')
            ->hasMigration('create_emails_table');
    }
}
