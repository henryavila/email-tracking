<?php

namespace AppsInteligentes\EmailTracking\Nova;

use AppsInteligentes\EmailTracking\Models\Email;
use AppsInteligentes\EmailTracking\Policies\EmailPolicy;
use Gate;
use Illuminate\Http\Request;

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

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        //
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
