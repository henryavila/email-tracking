<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

class Envelope
{
    public readonly ?string $transport;

    public readonly ?string $sender;

    public readonly ?string $sendingIp;

    public readonly ?string $targets;

    public function __construct(array $payload)
    {
        $this->transport = $payload['transport'] ?? null;
        $this->sender = $payload['sender'] ?? null;
        $this->sendingIp = $payload['sending-ip'] ?? null;
        $this->targets = $payload['targets'] ?? null;
    }
}
