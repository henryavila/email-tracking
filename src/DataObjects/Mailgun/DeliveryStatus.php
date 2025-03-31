<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

use Illuminate\Support\Facades\Log;

class DeliveryStatus
{
    public readonly bool $isTls;

    public readonly ?string $mxHost;

    public readonly ?int $code;

    public readonly ?string $description;

    public readonly bool $isUtf8;

    public readonly ?string $name;

    public readonly ?int $attemptNumber;

    public readonly ?string $deliveryMessage;

    public function __construct(public readonly ?array $rawData)
    {
        $this->isTls = $this->rawData['tls'] ?? true;
        $this->mxHost = $this->rawData['mx-host'] ?? null;
        $this->code = isset($this->rawData['code']) ? (int) $this->rawData['code'] : null;
        $this->description = $this->rawData['description'] ?? null;
        $this->isUtf8 = $this->rawData['utf8'] ?? true;
        $this->attemptNumber = isset($this->rawData['attempt-no']) ? (int) $this->rawData['attempt-no'] : null;
        $this->deliveryMessage = $this->rawData['message'] ?? null;
    }
}
