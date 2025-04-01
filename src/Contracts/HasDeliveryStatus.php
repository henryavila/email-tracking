<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Contracts;

interface HasDeliveryStatus
{
    public function initializeDeliveryStatus(array $payload): void;
}
