<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Contracts;

interface HasEmailFlags
{
    public function initializeFlags(array $payload): void;
}
