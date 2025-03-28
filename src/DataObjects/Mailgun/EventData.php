<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

use HenryAvila\EmailTracking\DataObjects\Mailgun\Message\Message;
use HenryAvila\EmailTracking\Enums\Mailgun\Event;

class EventData
{
    public readonly Event $event;

    public readonly string $timestamp;

    public readonly ?string $id;

    public readonly ?string $recipient;

    public readonly Message $message;

    public readonly Envelope $envelope;

    public readonly DeliveryStatus $deliveryStatus;

    public function __construct(public readonly ?array $rawData)
    {
        $this->message = new Message($rawData['message']);

        $this->validateData();

        // Always filled
        $this->event = Event::from($rawData['event']);
        $this->timestamp = (string) $rawData['timestamp'];
        $this->id = $rawData['id'];

        // Filled depending on the event
        $this->recipient = $rawData['recipient'] ?? null;
        $this->envelope = new Envelope($rawData['envelope']);
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

    public function getMessageId(): string
    {
        return $this->message->getMessageId();
    }
}
