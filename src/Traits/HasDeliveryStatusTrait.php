<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Traits;

use HenryAvila\EmailTracking\DataObjects\Mailgun\DeliveryStatus;

trait HasDeliveryStatusTrait
{
    public DeliveryStatus $deliveryStatus;

    public function initializeDeliveryStatus(array $payload): void
    {
        if (isset($payload['delivery-status'])) {
            $this->deliveryStatus = new DeliveryStatus($payload['delivery-status']);
        }
    }

    public function getDeliveryAttemptNumber(): int
    {
        return $this->deliveryStatus->attemptNumber;
    }

    public function hasDeliveryMessage(): bool
    {
        return ! empty($this->deliveryStatus->deliveryMessage);
    }

    public function getDeliveryMessage(): ?string
    {
        return $this->deliveryStatus->deliveryMessage;
    }
}
