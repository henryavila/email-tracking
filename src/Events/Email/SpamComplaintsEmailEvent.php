<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEnvelopeAndMessage;
use HenryAvila\EmailTracking\Traits\HasEnvelopeAndMessageTrait;

class SpamComplaintsEmailEvent extends AbstractEmailEvent implements HasEnvelopeAndMessage
{
    use HasEnvelopeAndMessageTrait;

    const CODE = 'complained';

    public function __construct(array $payload)
    {
        parent::__construct($payload);
        $this->initializeEnvelopeAndMessage($payload);
    }
}
