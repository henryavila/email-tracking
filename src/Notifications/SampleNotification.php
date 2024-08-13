<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SampleNotification extends Notification
{
    public function __construct(protected \Illuminate\Database\Eloquent\Model $model)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new TrackableNotificationMailMessage($this->model))
            ->subject('Sample Notification Test'.(now())->format('d/m/Y - h:i:s'))
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
