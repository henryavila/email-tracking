<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEmailFlags;
use HenryAvila\EmailTracking\Contracts\HasEnvelopeAndMessage;
use HenryAvila\EmailTracking\Traits\HasEmailFlagsTrait;
use HenryAvila\EmailTracking\Traits\HasEnvelopeAndMessageTrait;

class AcceptedEmailEvent extends AbstractEmailEvent implements HasEmailFlags, HasEnvelopeAndMessage
{
    use HasEmailFlagsTrait;
    use HasEnvelopeAndMessageTrait;

    const CODE = 'accepted';

    public string $recipientProvider;

    public string $method;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->initializeFlags($payload);
        $this->initializeEnvelopeAndMessage($payload);

        $this->recipientProvider = $payload['recipient-provider'] ?? '';
        $this->method = $payload['method'] ?? '';
    }
}
