<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HenryAvila\EmailTracking\EmailTracking
 */
class EmailTracking extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'email-tracking';
    }
}
