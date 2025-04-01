<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Factories;

use HenryAvila\EmailTracking\Events\Email\AbstractEmailEvent;
use HenryAvila\EmailTracking\Events\Email\AbstractFailureEmailEvent;
use HenryAvila\EmailTracking\Events\Email\AcceptedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\ClickedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;
use HenryAvila\EmailTracking\Events\Email\OpenedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\PermanentFailureEmailEvent;
use HenryAvila\EmailTracking\Events\Email\SpamComplaintsEmailEvent;
use HenryAvila\EmailTracking\Events\Email\TemporaryFailureEmailEvent;

class EmailEventFactory
{
    public static function make(array $payload): AbstractEmailEvent
    {
        return match (true) {
            $payload['event'] === AcceptedEmailEvent::CODE => new AcceptedEmailEvent($payload),
            $payload['event'] === ClickedEmailEvent::CODE => new ClickedEmailEvent($payload),
            $payload['event'] === SpamComplaintsEmailEvent::CODE => new SpamComplaintsEmailEvent($payload),
            $payload['event'] === DeliveredEmailEvent::CODE => new DeliveredEmailEvent($payload),
            $payload['event'] === OpenedEmailEvent::CODE => new OpenedEmailEvent($payload),
            $payload['event'] === AbstractFailureEmailEvent::CODE && $payload['severity'] === 'permanent' => new PermanentFailureEmailEvent($payload),
            $payload['event'] === AbstractFailureEmailEvent::CODE && $payload['severity'] === 'temporary' => new TemporaryFailureEmailEvent($payload),
            // Unsubscribe
            default => throw new \InvalidArgumentException('Invalid event type: '.$payload['event']),
        };
    }
}
