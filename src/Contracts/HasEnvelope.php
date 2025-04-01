<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Contracts;

interface HasEnvelope
{
    public function initializeEnvelope(array $payload): void;
}
