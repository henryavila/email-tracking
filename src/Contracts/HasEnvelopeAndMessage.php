<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Contracts;

interface HasEnvelopeAndMessage
{
    public function initializeEnvelopeAndMessage(array $payload): void;
}
