<?php

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEnvelopeAndMessage;
use HenryAvila\EmailTracking\DataObjects\Mailgun\Envelope;
use HenryAvila\EmailTracking\DataObjects\Mailgun\Message\Message;
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