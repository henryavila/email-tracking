<?php

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
            ->hasRoute('webhooks')
            ->hasMigration('create_emails_table')
            ->hasMigration('add_body_content_to_email_log');
    }
}
