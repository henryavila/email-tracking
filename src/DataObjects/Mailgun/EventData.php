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

    public readonly ?Envelope $envelope;

    public readonly ?DeliveryStatus $deliveryStatus;

    public ?Failed $failed = null;

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
        $this->envelope = isset($rawData['envelope']) ? new Envelope($rawData['envelope']) : null;
        $this->deliveryStatus = isset($rawData['delivery-status']) ? new DeliveryStatus($rawData['delivery-status']) : null;

        if ($this->isFailed()) {
            $this->failed = new Failed($this);
        }
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
        if (empty($this->rawData['event'])) {
            throw new \DomainException('Empty event on Mailgun hook');
        }

        if (empty($this->rawData['timestamp'])) {
            throw new \DomainException('Empty timestamp on Mailgun hook');
        }

        if (empty($this->rawData['id'])) {
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

    public function isFailed(): bool
    {
        return $this->eventIs(Event::FAILED);
    }

    public function isPermanentlyFailed(): bool
    {
        return $this->isFailed() && $this->failed->isPermanently;
    }

    public function isTemporaryFailed(): bool
    {
        return $this->isFailed() && ! $this->failed->isPermanently;
    }

}
