<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasDeliveryStatus;
use HenryAvila\EmailTracking\Contracts\HasEmailFlags;
use HenryAvila\EmailTracking\Contracts\HasEnvelope;
use HenryAvila\EmailTracking\Traits\HasDeliveryStatusTrait;
use HenryAvila\EmailTracking\Traits\HasEmailFlagsTrait;
use HenryAvila\EmailTracking\Traits\HasEnvelopeTrait;

class DeliveredEmailEvent extends AbstractEmailEvent implements HasDeliveryStatus, HasEmailFlags, HasEnvelope
{
    use HasDeliveryStatusTrait;
    use HasEmailFlagsTrait;
    use HasEnvelopeTrait;

    const CODE = 'delivered';

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->initializeFlags($payload);
        $this->initializeEnvelope($payload);
        $this->initializeDeliveryStatus($payload);
    }
}
