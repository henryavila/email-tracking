<?php

namespace HenryAvila\EmailTracking\Controllers;

use HenryAvila\EmailTracking\Middleware\Webhooks\MailgunWebhookMiddleware;
use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MailgunWebhookController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->middleware(MailgunWebhookMiddleware::class);
    }

    /**
     * Handles the Stripe Webhook call.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        try {
            $success = false;
            $data = $request->get('event-data');

            $message_id = $data['message']['headers']['message-id'] ?? null;

            if ($message_id === null) {
                Log::warning('Empty messageId on Mailgun hook', [
                    'message' => $data['message'] ?? null,
                    'headers' => $data['message']['headers'] ?? null,
                    'full' => $data,
                ]);
            }

            /** @var Email $email */
            $email = Email::where('message_id', $message_id)->first();


            if ($email) {
                if ($data['event'] === 'opened' || $data['event'] === 'clicked') {
                    $email->{$data['event']}++;

                    $firstField = 'first_' . $data['event'] . '_at';
                    $lastField = 'last_' . $data['event'] . '_at';

                    if (isset($email->{$firstField})) {
                        $email->{$lastField} = now();
                    } else {
                        $email->{$firstField} = now();
                    }
                }

                if ($data['event'] === 'delivered' || $data['event'] === 'failed') {
                    $email->{$data['event'] . '_at'} = now();
                }

                if (isset($data['delivery-status']['attempt-no'])) {
                    $email->delivery_status_attempts = $data['delivery-status']['attempt-no'];
                }

                if (isset($data['delivery-status']['message'])) {
                    $email->delivery_status_message = $email->delivery_status_message ?? '';
                    $join = empty($email->delivery_status_message) ? '' : '||'; // we will not add the join string if this is the first message
                    $email->delivery_status_message .= $join . now()->format('d/m/Y H:i:s') . ' - ' . $data['delivery-status']['message'];
                }

                $email->save();
                $success = true;
            }

            return response()->json(['success' => $success]);
        } catch (\Exception $exception) {
            Log::error('Mailgun webhook', $exception);
        }
    }
}
