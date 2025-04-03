<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEmailFlags;
use HenryAvila\EmailTracking\Contracts\HasEnvelope;
use HenryAvila\EmailTracking\Traits\HasEmailFlagsTrait;
use HenryAvila\EmailTracking\Traits\HasEnvelopeTrait;

class AcceptedEmailEvent extends AbstractEmailEvent implements HasEmailFlags, HasEnvelope
{
    use HasEmailFlagsTrait;
    use HasEnvelopeTrait;

    const CODE = 'accepted';

    public string $recipientProvider;

    public string $method;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->initializeFlags($payload);
        $this->initializeEnvelope($payload);

        $this->recipientProvider = $payload['recipient-provider'] ?? '';
        $this->method = $payload['method'] ?? '';
    }
}
