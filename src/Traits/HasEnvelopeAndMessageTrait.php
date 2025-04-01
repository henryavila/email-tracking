<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Traits;

use HenryAvila\EmailTracking\DataObjects\Mailgun\Envelope;
use HenryAvila\EmailTracking\DataObjects\Mailgun\Message\Message;

trait HasEnvelopeAndMessageTrait
{
    public Envelope $envelope;

    public Message $message;

    public array $campaigns = [];

    public string $recipient = '';

    public string $recipientDomain = '';

    public array $tags = [];

    public function initializeEnvelopeAndMessage(array $payload): void
    {
        if (isset($payload['envelope'])) {
            $this->envelope = new Envelope($payload['envelope']);

        }
        if (isset($payload['message'])) {
            $this->message = new Message($payload['message']);
        }

        $this->campaigns = $payload['campaigns'] ?? [];
        $this->recipient = $payload['recipient'] ?? '';
        $this->recipientDomain = $payload['recipient-domain'] ?? '';
        $this->tags = $payload['tags'] ?? [];

    }
}
