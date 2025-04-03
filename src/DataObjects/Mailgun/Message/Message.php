<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun\Message;

class Message
{
    public readonly ?array $attachments;

    public readonly ?int $size;

    public readonly ?MessageHeaders $headers;

    public function __construct(?array $payload)
    {
        $this->attachments = $payload['attachments'] ?? null;
        $this->size = isset($payload['size']) ? (int) $payload['size'] : null;
        $this->headers = new MessageHeaders($payload['headers']);
    }

    public function getMessageId(): string
    {
        return $this->headers->messageId;
    }
}
