<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasClientInfo;
use HenryAvila\EmailTracking\Traits\HasClientInfoTrait;

class UnsubscribeEmailEvent extends AbstractEmailEvent implements HasClientInfo
{
    use HasClientInfoTrait;

    const CODE = 'unsubscribed';

    public ?string $ip;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->ip = $payload['ip'] ?? null;
        $this->initializeClientInfo($payload);
    }
}
