<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

class Envelope
{
    public readonly ?string $transport;

    public readonly ?string $sender;

    public readonly ?string $sendingIp;

    public readonly ?string $targets;

    public function __construct(public readonly ?array $rawData)
    {
        $this->transport = $rawData['transport'] ?? null;
        $this->sender = $rawData['sender'] ?? null;
        $this->sendingIp = $rawData['sending-ip'] ?? null;
        $this->targets = $rawData['targets'] ?? null;
    }
}
