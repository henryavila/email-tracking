<?php

namespace HenryAvila\EmailTracking\Contracts;

interface HasEnvelopeAndMessage
{
    public function initializeEnvelopeAndMessage(array $payload): void;
}