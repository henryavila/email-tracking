<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Listeners;

use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class LogEmailSentListener
{
    /**
     * Handle the MessageSent event.
     *
     * This listener is triggered when an email is sent. It creates a record in the
     * emails table with all relevant information including recipients, subject, and
     * optionally the email type for categorization.
     *
     * If the mailable implements getEmailType(), the email will be categorized
     * accordingly for better organization, filtering, and analytics.
     *
     * @param  MessageSent  $event  The email sent event
     */
    public function handle(MessageSent $event): void
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

        // Capture email_type if available (set by TrackableMail::buildViewData())
        if (isset($event->data['__email_type'])) {
            $data['email_type'] = $event->data['__email_type'];
        }

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
