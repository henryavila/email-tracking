<?php

namespace AppsInteligentes\EmailTracking;

use Laravel\Nova\Fields\MorphMany;

class EmailTracking
{
    public static function hasManyEmailsField(): MorphMany
    {
        return MorphMany::make(__('email-tracking::resources.emails'), 'emails', \AppsInteligentes\EmailTracking\Nova\EmailResource::class);
    }
}
