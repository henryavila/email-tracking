<?php

namespace AppsInteligentes\EmailTracking\Nova;

use AppsInteligentes\EmailTracking\Models\Email;
use AppsInteligentes\EmailTracking\Policies\EmailPolicy;
use Gate;

class EmailTrackingTool extends \Laravel\Nova\Tool
{
    public static string $emailResource = EmailResource::class;
    public string $emailPolicy = EmailPolicy::class;

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        \Laravel\Nova\Nova::resources([
            static::$emailResource,
        ]);

        Gate::policy(Email::class, $this->emailPolicy);
    }

    public function emailResource(string $emailResource): EmailTrackingTool
    {
        static::$emailResource = $emailResource;

        return $this;
    }

    public function emailPolicy(string $emailPolicy)
    {
        $this->emailPolicy = $emailPolicy;

        return $this;
    }
}
