<?php

namespace AppsInteligentes\EmailTracking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AppsInteligentes\EmailTracking\EmailTracking
 */
class EmailTracking extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'email-tracking';
    }
}
