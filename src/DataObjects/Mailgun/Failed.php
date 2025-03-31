<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

use HenryAvila\EmailTracking\Enums\Mailgun\Event;

class Failed
{
    public readonly ?string $reason;

    public readonly ?bool $isPermanently;

    public function __construct(EventData $eventData)
    {
        if ($eventData->eventIs(Event::FAILED)) {
            $this->reason = $eventData->rawData['reason'] ?? null;
            $this->isPermanently = $eventData->rawData['severity'] === 'permanent' ?? null;
        }
    }
}
