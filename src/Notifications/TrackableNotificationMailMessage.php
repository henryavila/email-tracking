<?php

namespace AppsInteligentes\EmailTracking\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * This class will allow to track and link the e-mail sender owner
 */
class TrackableNotificationMailMessage extends MailMessage
{
    public function __construct(public Model $model)
    {
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['model'] = $this->model;

        return $array;
    }
}
