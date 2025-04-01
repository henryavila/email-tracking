<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Traits;

trait HasEmailFlagsTrait
{
    public ?bool $isRouted = null;

    public ?bool $isAuthenticated = null;

    public ?bool $isSystemTest = null;

    public ?bool $isTestMode = null;

    public function initializeFlags(array $payload): void
    {
        $this->isRouted = $payload['flags']['is-routed'] ?? null;
        $this->isAuthenticated = $payload['flags']['is-authenticated'] ?? null;
        $this->isSystemTest = $payload['flags']['is-system-test'] ?? null;
        $this->isTestMode = $payload['flags']['is-test-mode'] ?? null;
    }
}
