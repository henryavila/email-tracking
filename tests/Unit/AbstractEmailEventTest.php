<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\ClickedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;
use HenryAvila\EmailTracking\Events\Email\OpenedEmailEvent;

beforeEach(function () {
    $this->deliveredPayload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered.json'),
        true
    );

    $this->openedPayload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/opened.json'),
        true
    );
});

it('returns message ID from nested headers', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    expect($event->getMessageId())
        ->toBe('20130503182626.18666.16540@alertas.crcmg.org.br')
        ->and($event->message->getMessageId())
        ->toBe('20130503182626.18666.16540@alertas.crcmg.org.br');
});

it('checks if event is any of specified types using isAnyOf', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    expect($event->isAnyOf([DeliveredEmailEvent::class, OpenedEmailEvent::class]))
        ->toBeTrue()
        ->and($event->isAnyOf([OpenedEmailEvent::class, ClickedEmailEvent::class]))
        ->toBeFalse()
        ->and($event->isAnyOf([DeliveredEmailEvent::class]))
        ->toBeTrue();
});

it('returns false for isAnyOf with empty array', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    expect($event->isAnyOf([]))->toBeFalse();
});

it('getRecipientWithName returns header when single recipient', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    // Single recipient in "to" header
    expect($event->getRecipientWithName())
        ->toBe('Alice <alice@example.com>')
        ->and($event->recipient)
        ->toBe('alice@example.com');
});

it('getRecipientWithName returns plain recipient when multiple recipients in header', function () {
    $payload = $this->deliveredPayload;
    $payload['message']['headers']['to'] = 'Alice <alice@example.com>, Bob <bob@example.com>';
    $payload['recipient'] = 'alice@example.com';

    $event = new DeliveredEmailEvent($payload);

    // When multiple recipients, returns the plain recipient
    expect($event->getRecipientWithName())
        ->toBe('alice@example.com')
        ->not->toContain(',');
});

it('converts event to JSON string using __toString', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    $jsonString = (string) $event;

    expect($jsonString)
        ->toBeString()
        ->toContain('"event": "delivered"')
        ->toContain('"recipient": "alice@example.com"')
        ->toContain('"timestamp":')
        ->and(json_decode($jsonString, true))
        ->toBeArray()
        ->toHaveKey('event')
        ->toHaveKey('recipient');
});

it('initializes all properties from payload correctly', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    expect($event->timestamp)->toBeString()
        ->and($event->timestamp)->toBe('1521472262.908181')
        ->and($event->id)->toBeString()
        ->and($event->id)->toBe('CPgfbmQMTCKtHW6uIWtuVe')
        ->and($event->recipient)->toBeString()
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBeString()
        ->and($event->recipientDomain)->toBe('example.com')
        ->and($event->message)->toBeInstanceOf(HenryAvila\EmailTracking\DataObjects\Mailgun\Message\Message::class);
});

it('handles payload with null recipient and recipient-domain', function () {
    $payload = $this->deliveredPayload;
    unset($payload['recipient'], $payload['recipient-domain']);

    $event = new DeliveredEmailEvent($payload);

    expect($event->recipient)->toBeNull()
        ->and($event->recipientDomain)->toBeNull();
});

it('payload is readonly and accessible', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    expect($event->payload)
        ->toBeArray()
        ->toHaveKey('event')
        ->and($event->payload['event'])->toBe('delivered')
        ->and($event->payload['recipient'])->toBe('alice@example.com');
});

it('handles different event types with isAnyOf', function () {
    $deliveredEvent = new DeliveredEmailEvent($this->deliveredPayload);
    $openedEvent = new OpenedEmailEvent($this->openedPayload);

    expect($deliveredEvent->isAnyOf([DeliveredEmailEvent::class]))
        ->toBeTrue()
        ->and($openedEvent->isAnyOf([DeliveredEmailEvent::class]))
        ->toBeFalse()
        ->and($openedEvent->isAnyOf([OpenedEmailEvent::class, ClickedEmailEvent::class]))
        ->toBeTrue();
});

it('preserves original payload structure in __toString', function () {
    $event = new DeliveredEmailEvent($this->deliveredPayload);

    $jsonString = (string) $event;
    $decodedPayload = json_decode($jsonString, true);

    // Verify all major keys are preserved
    expect($decodedPayload)
        ->toHaveKeys(['id', 'timestamp', 'event', 'recipient', 'recipient-domain', 'message'])
        ->and($decodedPayload['message'])
        ->toHaveKey('headers')
        ->and($decodedPayload['message']['headers'])
        ->toHaveKeys(['to', 'from', 'subject', 'message-id']);
});

it('creates event from minimal valid payload', function () {
    $minimalPayload = [
        'id' => 'test-id-123',
        'timestamp' => '1234567890.123456',
        'event' => 'delivered',
        'message' => [
            'headers' => [
                'message-id' => 'test-message-id@example.com',
                'to' => 'recipient@example.com',
                'from' => 'sender@example.com',
                'subject' => 'Test',
            ],
        ],
    ];

    $event = new DeliveredEmailEvent($minimalPayload);

    expect($event->id)->toBe('test-id-123')
        ->and($event->timestamp)->toBe('1234567890.123456')
        ->and($event->recipient)->toBeNull()
        ->and($event->recipientDomain)->toBeNull()
        ->and($event->getMessageId())->toBe('test-message-id@example.com');
});
