<?php

namespace HenryAvila\EmailTracking\Events\Email;

class TemporaryFailureEmailEvent extends AbstractFailureEmailEvent
{
    public bool $permanent = false;

    public function getRetrySeconds(): ?int
    {
        return $this->deliveryStatus?->retrySeconds;
    }
}