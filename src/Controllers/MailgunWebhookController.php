<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Controllers;

use HenryAvila\EmailTracking\Events\Email\AbstractEmailEvent;
use HenryAvila\EmailTracking\Events\Email\ClickedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;
use HenryAvila\EmailTracking\Events\Email\OpenedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\PermanentFailureEmailEvent;
use HenryAvila\EmailTracking\Events\Email\TemporaryFailureEmailEvent;
use HenryAvila\EmailTracking\Events\EmailWebhookProcessed;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;
use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MailgunWebhookController // extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            /** @var AbstractEmailEvent $emailEvent */
            $emailEvent = EmailEventFactory::make($request->get('event-data'));
            $email = Email::where('message_id', $emailEvent->getMessageId())->first();

            if ($email === null) {
                Log::warning('Email not found', [
                    'message_id' => $emailEvent->getMessageId(),
                    'payload' => $emailEvent->payload,
                ]);

                return abort(404, 'Email not found');
            }

            /**
             * @var OpenedEmailEvent|ClickedEmailEvent $emailEvent
             */
            if ($emailEvent->isAnyOf([OpenedEmailEvent::class, ClickedEmailEvent::class])) {
                $email->{$emailEvent::CODE}++;

                $firstField = 'first_' . $emailEvent::CODE . '_at';
                $lastField = 'last_' . $emailEvent::CODE . '_at';

                if (isset($email->{$firstField})) {
                    $email->{$lastField} = now();
                } else {
                    $email->{$firstField} = now();
                }
            }

            if ($emailEvent instanceof TemporaryFailureEmailEvent) {
                $email->delivery_status_attempts = $emailEvent->getDeliveryAttemptNumber();
            }

            /**
             * @var DeliveredEmailEvent|PermanentFailureEmailEvent $emailEvent
             */
            if ($emailEvent->isAnyOf([DeliveredEmailEvent::class, PermanentFailureEmailEvent::class])) {

                $email->{$emailEvent::CODE . '_at'} = now();
                $email->delivery_status_attempts = $emailEvent->getDeliveryAttemptNumber();

                if ($emailEvent->hasDeliveryMessage()) {
                    $logLine = now()->format('d/m/Y H:i:s') . ' - ' . $emailEvent->getDeliveryMessage();
                    $messages = empty($email->delivery_status_message)
                        ? []
                        : explode('||', $email->delivery_status_message);
                    $messages[] = $logLine;
                    $email->delivery_status_message = implode('||', $messages);

                }
            }

            $email->save();
            EmailWebhookProcessed::dispatch($emailEvent);

            return response()->json(['success' => true]);
        } catch (\Exception $exception) {

            Log::error(
                'Mailgun webhook',
                [
                    'message' => $exception->getMessage(),
                    'stack' => $exception->getTrace(),
                ]
            );
            abort(500);

        }
    }
}
