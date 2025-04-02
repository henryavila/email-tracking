<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events;

use HenryAvila\EmailTracking\Events\Email\AbstractEmailEvent;
use Illuminate\Foundation\Events\Dispatchable;

class EmailWebhookProcessed
{
    use Dispatchable;

    public function __construct(public readonly AbstractEmailEvent $emailEvent) {}
}
