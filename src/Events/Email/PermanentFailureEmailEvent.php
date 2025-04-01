<?php

namespace HenryAvila\EmailTracking\Events\Email;

class PermanentFailureEmailEvent extends AbstractFailureEmailEvent
{
    public bool $permanent = true;
}