<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun\Message;

class MessageHeaders
{
    public readonly string $messageId;

    public readonly ?string $from;

    public readonly ?string $to;

    public readonly ?string $subject;

    public function __construct(?array $payload)
    {
        if (empty($payload['message-id'])) {
            throw new \InvalidArgumentException('Message ID is required');
        }

        $this->messageId = $payload['message-id'];
        $this->from = $payload['from'] ?? null;
        $this->to = $payload['to'] ?? null;
        $this->subject = $payload['subject'] ?? null;
    }
}
