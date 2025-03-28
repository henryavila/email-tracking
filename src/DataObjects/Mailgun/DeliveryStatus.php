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

    public readonly ?bool $isUtf8;

    public readonly ?string $name;

    public readonly ?int $attemptNumber;

    public readonly ?string $deliveryMessage;

    public function __construct(public readonly ?array $rawData)
    {
        $this->validateData();

        $this->isTls = $this->rawData['tls'];
        $this->mxHost = $this->rawData['mx-host'];
        $this->code = isset($this->rawData['code']) ? (int) $this->rawData['code'] : null;
        $this->description = $this->rawData['description'] ?? null;
        $this->isUtf8 = $this->rawData['utf8'];
        $this->attemptNumber = isset($this->rawData['attempt-no']) ? (int) $this->rawData['attempt-no'] : null;
        $this->deliveryMessage = $this->rawData['message'];
    }

    private function validateData(): void
    {
        if (empty($this->rawData) || empty($this->rawData['session-seconds']) || empty($this->rawData['mx-host'])) {
            $invalidMessage = 'Invalid Mailgun Delivery Status webhook data';
            Log::warning($invalidMessage, $this->rawData);

            throw new \DomainException($invalidMessage);
        }
    }
}
