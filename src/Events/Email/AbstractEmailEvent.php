<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\DataObjects\Mailgun\Message\Message;

class AbstractEmailEvent
{
    public string $timestamp;

    public string $id;

    public Message $message;

    public ?string $recipient;

    public ?string $recipientDomain;

    public function __construct(public readonly array $payload)
    {
        $this->timestamp = (string) $payload['timestamp'];
        $this->id = $payload['id'];

        $this->recipient = $payload['recipient'] ?? null;
        $this->recipientDomain = $payload['recipient-domain'] ?? null;

        if (isset($payload['message'])) {
            $this->message = new Message($payload['message']);
        }
    }

    public function getMessageId(): string
    {
        return $this->message->getMessageId();
    }

    public function isAnyOf(array $eventTypes): bool
    {
        foreach ($eventTypes as $type) {
            if ($this instanceof $type) {
                return true;
            }
        }

        return false;
    }

    public function isFailure(): bool
    {
        return self::class === AbstractFailureEmailEvent::CODE;
    }

    public function getRecipientWithName(): ?string
    {
        $header = $this->message->headers->to;
        if (str_contains($header, ',')) {
            return $this->recipient;
        }

        return $header;
    }

    public function __toString(): string
    {
        return json_encode($this->payload, JSON_PRETTY_PRINT);
    }
}
