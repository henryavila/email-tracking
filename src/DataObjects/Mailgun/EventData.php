<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

use HenryAvila\EmailTracking\DataObjects\Mailgun\Message\Message;
use HenryAvila\EmailTracking\Enums\Mailgun\Event;
use Illuminate\Support\Facades\Log;

class EventData
{
    public readonly Event $event;

    public readonly string $timestamp;

    public readonly ?string $id;

    public readonly ?string $recipient;

    public readonly Message $message;

    public readonly DeliveryStatus $deliveryStatus;

    /**
     * The model ID
     */
    public readonly ?string $messageId;

    public function __construct(public readonly ?array $rawData)
    {
        // TODO: Remove. It should be dont just in DataObject
        $this->messageId = $rawData['message']['headers']['message-id'] ?? null;

        $this->validateData();

        // Always filled
        $this->event = Event::from($rawData['event']);
        $this->timestamp = (string) $rawData['timestamp'];
        $this->id = $rawData['id'];

        // Filled depending on the event
        $this->recipient = $rawData['recipient'] ?? null;

        $this->message = new Message($rawData['message']);

        // TODO: Crate Data Object for: Envelope

        $this->deliveryStatus = new DeliveryStatus($rawData['deliveryStatus']);
    }

    public function hasDeliveryMessage(): bool
    {
        return $this->getDeliveryMessage() !== null;
    }

    public function getDeliveryMessage(): ?string
    {
        return $this->deliveryStatus?->deliveryMessage;
    }

    public function getDeliveryAttemptNumber(): ?int
    {
        return $this?->deliveryStatus?->attemptNumber;
    }

    private function validateData(): void
    {
        if ($this->messageId === null) {
            Log::warning('Empty messageId on Mailgun hook', $this->rawData);
            throw new \DomainException('Empty messageId on Mailgun hook');
        }

        if (empty($data['event'])) {
            throw new \DomainException('Empty event on Mailgun hook');
        }

        if (empty($data['timestamp'])) {
            throw new \DomainException('Empty timestamp on Mailgun hook');
        }

        if (empty($data['id'])) {
            throw new \DomainException('Empty id on Mailgun hook');
        }
    }

    public function eventIs(Event $event): bool
    {
        return $this->event === $event;
    }

    /**
     * @param  Event[]  $events
     */
    public function eventIsAny(array $events): bool
    {
        return in_array($this->event, $events, true);
    }
}
