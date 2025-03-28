<?php

namespace HenryAvila\EmailTracking\DataObjects\Mailgun\Message;

class MessageHeaders
{
    public readonly string $messageId;
    public readonly ?string $from;
    public readonly ?string $to;
    public readonly ?string $subject;

    public function __construct(public readonly ?string $rawData)
    {
        $this->validateData();

        $this->messageId = $rawData['message-id'];
        $this->from = $rawData['from'] ?? null;
        $this->to = $rawData['to'] ?? null;
        $this->subject = $rawData['subject'] ?? null;
    }

    public function validateData(): void
    {
        if (empty($this->rawData) || empty($this->rawData['message-id'])) {
            throw new \DomainException('Message id not found on Message headers');
        }
    }

}