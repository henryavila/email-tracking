<?php

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasClientInfo;
use HenryAvila\EmailTracking\Contracts\HasDeliveryStatus;
use HenryAvila\EmailTracking\Traits\HasClientInfoTrait;
use HenryAvila\EmailTracking\Traits\HasDeliveryStatusTrait;

class OpenedEmailEvent extends AbstractEmailEvent implements HasClientInfo
{
    use HasClientInfoTrait;

    const CODE = 'opened';

    public ?string $ip;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->ip = $payload['ip'] ?? null;
        $this->initializeClientInfo($payload);
    }

}