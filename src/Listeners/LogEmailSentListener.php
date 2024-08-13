<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Listeners;

use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class LogEmailSentListener
{
    /**
     * This will be trigged when an e-mail will be sent.
     * If the Mail sender called registerSender(), the sender will be linked to Email object
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $data = [
            'message_id' => preg_replace('([<>])', '', $event->sent->getMessageId()),
            'subject' => $event->message->getSubject(),

            'to' => collect($event->message->getTo())
                ->map(fn (Address $address) => $address->getAddress())
                ->implode(', '),

            'cc' => collect($event->message->getCc())
                ->map(fn (Address $address) => $address->getAddress())
                ->implode(', '),

            'bcc' => collect($event->message->getBcc())
                ->map(fn (Address $address) => $address->getAddress())
                ->implode(', '),

            'reply_to' => collect($event->message->getReplyTo())
                ->map(fn (Address $address) => $address->getAddress())
                ->implode(', '),
        ];

        if (config('email-tracking.log-body-html')) {
            $data['body_html'] = $event->message->getHtmlBody();
        }

        if (config('email-tracking.log-body-txt')) {
            $data['body_txt'] = $event->message->getTextBody();
        }

        $model = $event->data['model'] ?? null;

        if ($model && is_object($model) && method_exists($model, 'emails')) {
            $model->emails()->create($data);
        } else {
            Email::create($data);
        }
    }
}
