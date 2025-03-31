<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Enums\Mailgun;

enum Event: string
{
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case OPENED = 'opened';
    case CLICKED = 'clicked';
    case UNSUBSCRIBED = 'unsubscribed';
    case COMPLAINED = 'complained';
    case STORED = 'stored';

}
