<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun\Message;

use Illuminate\Support\Facades\Log;

class Message
{
    public readonly ?array $attachments;

    public readonly ?int $size;

    public readonly ?MessageHeaders $headers;

    public function __construct(public readonly ?array $rawData)
    {
        $this->validateData();

        $this->attachments = $this->rawData['attachments'] ?? null;
        $this->size = isset($this->rawData['size']) ? (int) $this->rawData['size'] : null;
        $this->headers = new MessageHeaders($this->rawData['headers']);
    }

    public function getMessageId(): string
    {
        return $this->headers->messageId;
    }

    private function validateData(): void
    {
        if (empty($this->rawData) || empty($this->rawData['headers'])) {
            throw new \DomainException('The message dont contain the headers data');
        }

        if (($this->rawData['headers']['message-id'] ?? null) === null) {
            Log::warning('Empty messageId on Mailgun hook message', $this->rawData);
            throw new \DomainException('Empty messageId on Mailgun hook');
        }

    }
}
