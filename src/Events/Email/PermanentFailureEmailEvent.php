<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

class PermanentFailureEmailEvent extends AbstractFailureEmailEvent
{
    public bool $permanent = true;
}
