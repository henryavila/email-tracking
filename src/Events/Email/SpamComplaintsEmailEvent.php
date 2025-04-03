<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEnvelope;
use HenryAvila\EmailTracking\Traits\HasEnvelopeTrait;

class SpamComplaintsEmailEvent extends AbstractEmailEvent implements HasEnvelope
{
    use HasEnvelopeTrait;

    const CODE = 'complained';

    public function __construct(array $payload)
    {
        parent::__construct($payload);
        $this->initializeEnvelope($payload);
    }
}
