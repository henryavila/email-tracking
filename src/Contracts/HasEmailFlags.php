<?php

namespace HenryAvila\EmailTracking\Contracts;

interface HasEmailFlags
{
    public function initializeFlags(array $payload): void;
}