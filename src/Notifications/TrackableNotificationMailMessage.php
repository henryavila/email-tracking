<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

/**
 * This class will allow to track and link the e-mail sender owner
 */
class TrackableNotificationMailMessage extends MailMessage
{
    public function __construct(public $model = null) {}

    public function toArray(): array
    {
        $array = parent::toArray();
        if ($this->model !== null) {
            $array['model'] = $this->model;
        }

        return $array;
    }

    public function blankLine(): self
    {
        return $this->line(new HtmlString('<p></p><br />'));
    }

    public function blankLineIf(bool $condition): self
    {
        return $condition ?
            $this->blankLine() :
            $this;
    }
}
