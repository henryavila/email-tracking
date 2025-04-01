<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

class ClientInfo
{
    public ?string $clientName;

    public ?string $clientOs;

    public ?string $clientType;

    public ?string $deviceType;

    public ?string $userAgent;

    public ?string $bot;

    public function __construct(array $payload)
    {
        $this->clientName = $payload['client-name'] ?? null;
        $this->clientOs = $payload['client-os'] ?? null;
        $this->clientType = $payload['client-type'] ?? null;
        $this->deviceType = $payload['device-type'] ?? null;
        $this->userAgent = $payload['user-agent'] ?? null;
        $this->bot = $payload['bot'] ?? null;
    }
}
