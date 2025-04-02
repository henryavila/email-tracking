<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasDeliveryStatus;
use HenryAvila\EmailTracking\Contracts\HasEmailFlags;
use HenryAvila\EmailTracking\Contracts\HasEnvelope;
use HenryAvila\EmailTracking\Traits\HasDeliveryStatusTrait;
use HenryAvila\EmailTracking\Traits\HasEmailFlagsTrait;
use HenryAvila\EmailTracking\Traits\HasEnvelopeTrait;

class AbstractFailureEmailEvent extends AbstractEmailEvent implements HasDeliveryStatus, HasEmailFlags, HasEnvelope
{
    use HasDeliveryStatusTrait, HasEmailFlagsTrait, HasEnvelopeTrait;

    const CODE = 'failed';

    public string $reason;

    public bool $permanent;

    public function __construct(array $payload)
    {
        parent::__construct($payload);
        $this->reason = $payload['reason'];

        $this->initializeFlags($payload);
        $this->initializeEnvelope($payload);
        $this->initializeDeliveryStatus($payload);
    }

    public function getFullErrorMessage(): string
    {
        $reason = $this->reason;
        $message = $this->getDeliveryMessage();
        $description = $this->deliveryStatus->description;

        return implode(" | ", array_filter([$reason, $message, $description]));
    }
}
