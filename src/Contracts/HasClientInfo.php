<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Contracts;

interface HasClientInfo
{
    public function initializeClientInfo(array $payload): void;
}
