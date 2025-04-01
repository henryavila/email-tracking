<?php

namespace HenryAvila\EmailTracking\Factories;

use HenryAvila\EmailTracking\Events\Email;

class EmailEventFactory
{
    public static function make(array $payload): Email\AbstractEmailEvent
    {
        return match ($payload['event']) {
            Email\AcceptedEmailEvent::CODE => new Email\AcceptedEmailEvent($payload),
            Email\ClickedEmailEvent::CODE => new Email\ClickedEmailEvent($payload),
            Email\SpamComplaintsEmailEvent::CODE => new Email\SpamComplaintsEmailEvent($payload),
            Email\DeliveredEmailEvent::CODE => new Email\DeliveredEmailEvent($payload),

            default => throw new \InvalidArgumentException('Invalid event type: '.$payload['event']),
        };
    }
}