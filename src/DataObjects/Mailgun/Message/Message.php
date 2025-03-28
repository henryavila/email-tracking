<?php

namespace HenryAvila\EmailTracking\DataObjects\Mailgun\Message;

class Message
{
    public readonly ?array $attachments;
    public readonly ?int $size;

    public readonly ?MessageHeaders $headers;

    public function __construct(public readonly ?string $rawData)
    {
        $this->validateData();

        $this->attachments = $this->rawData['attachments'] ?? null;
        $this->size = isset($this->rawData['size'])? (int) $this->rawData['size'] : null;
        $this->headers = new MessageHeaders($this->rawData['headers']);
    }

    public function validateData(): void
    {
        if (empty($this->rawData) || empty($this->rawData['headers'])) {
            throw new \DomainException('The message dont contain the headers data');
        }

    }

}