<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Controllers;

use HenryAvila\EmailTracking\DataObjects\Mailgun\EventData;
use HenryAvila\EmailTracking\Events\EmailWebhookProcessed;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;
use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MailgunWebhookController // extends Controller
{
    public function __invoke(Request $request)
    {

        $emailEvent = EmailEventFactory::make($request->get('event-data'));
        dump($emailEvent);
        /*
        try {
            $eventData = new EventData($request->get('event-data'));

            $email = Email::where('message_id', $eventData->getMessageId())->first();

            if ($email === null) {
                Log::warning('Email not found', [
                    'message_id' => $eventData->getMessageId(),
                    'data' => $eventData->rawData,
                ]);

                return abort(404, 'Email not found');
            }

            if ($eventData->eventIsAny([Event::OPENED, Event::CLICKED])) {
                $email->{$eventData->event->value}++;

                $firstField = 'first_'.$eventData->event->value.'_at';
                $lastField = 'last_'.$eventData->event->value.'_at';

                if (isset($email->{$firstField})) {
                    $email->{$lastField} = now();
                } else {
                    $email->{$firstField} = now();
                }
            }

            if ($eventData->eventIsAny([Event::DELIVERED, Event::FAILED])) {
                $email->{$eventData->event->value.'_at'} = now();
            }

            $email->delivery_status_attempts = $eventData->getDeliveryAttemptNumber();

            if ($eventData->hasDeliveryMessage()) {
                $logLine = now()->format('d/m/Y H:i:s').' - '.$eventData->getDeliveryMessage();
                $messages = empty($email->delivery_status_message)
                    ? []
                    : explode('||', $email->delivery_status_message);
                $messages[] = $logLine;
                $email->delivery_status_message = implode('||', $messages);

            }

            $email->save();

            EmailWebhookProcessed::dispatch($eventData);

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
        */
    }
}
