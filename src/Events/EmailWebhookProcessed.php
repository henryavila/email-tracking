<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events;

use HenryAvila\EmailTracking\DataObjects\Mailgun\EventData;
use Illuminate\Foundation\Events\Dispatchable;

class EmailWebhookProcessed
{
    use Dispatchable;

    public function __construct(EventData $eventData) {}
}
