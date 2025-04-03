<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasClientInfo;
use HenryAvila\EmailTracking\Contracts\HasEnvelope;
use HenryAvila\EmailTracking\Traits\HasClientInfoTrait;
use HenryAvila\EmailTracking\Traits\HasEnvelopeTrait;

class ClickedEmailEvent extends AbstractEmailEvent implements HasClientInfo, HasEnvelope
{
    use HasClientInfoTrait;
    use HasEnvelopeTrait;

    const CODE = 'clicked';

    public string $ip;

    public string $url;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->initializeEnvelope($payload);
        $this->initializeClientInfo($payload);

        $this->ip = $payload['ip'] ?? '';
        $this->url = $payload['url'] ?? '';

    }
}
