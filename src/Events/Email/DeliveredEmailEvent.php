<?php

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEmailFlags;
use HenryAvila\EmailTracking\Contracts\HasEnvelopeAndMessage;
use HenryAvila\EmailTracking\DataObjects\Mailgun\DeliveryStatus;
use HenryAvila\EmailTracking\Traits\HasEmailFlagsTrait;
use HenryAvila\EmailTracking\Traits\HasEnvelopeAndMessageTrait;

class DeliveredEmailEvent extends AbstractEmailEvent implements HasEmailFlags, HasEnvelopeAndMessage
{
    use HasEmailFlagsTrait;
    use HasEnvelopeAndMessageTrait;

    const CODE = 'delivered';
    public DeliveryStatus $deliveryStatus;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->initializeFlags($payload);
        $this->initializeEnvelopeAndMessage($payload);

        $this->deliveryStatus = new DeliveryStatus($payload['delivery-status']);
    }
}