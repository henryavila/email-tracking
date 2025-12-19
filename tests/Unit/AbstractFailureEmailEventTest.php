<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\PermanentFailureEmailEvent;
use HenryAvila\EmailTracking\Events\Email\TemporaryFailureEmailEvent;

beforeEach(function () {
    $this->permanentFailurePayload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/failed-permanent.json'),
        true
    );

    $this->temporaryFailurePayload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/failed-temporary.json'),
        true
    );
});

it('builds full error message from reason, delivery message, and description', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    $fullErrorMessage = $event->getFullErrorMessage();

    expect($fullErrorMessage)
        ->toBeString()
        ->toContain('suppress-bounce') // reason
        ->toContain('Not delivering to previously bounced address'); // description
});

it('filters out empty parts in full error message', function () {
    // Create payload with empty delivery message
    $payload = $this->permanentFailurePayload;
    $payload['delivery-status']['message'] = '';
    $payload['delivery-status']['description'] = '';
    $payload['reason'] = 'test-reason';

    $event = new PermanentFailureEmailEvent($payload);

    $fullErrorMessage = $event->getFullErrorMessage();

    expect($fullErrorMessage)
        ->toBe('test-reason')
        ->not->toContain('|'); // No separators when other parts are empty
});

it('combines all three parts when all are present', function () {
    $payload = $this->temporaryFailurePayload;
    $payload['delivery-status']['message'] = 'SMTP Error';
    $payload['delivery-status']['description'] = 'Connection timeout';
    $payload['reason'] = 'generic';

    $event = new TemporaryFailureEmailEvent($payload);

    $fullErrorMessage = $event->getFullErrorMessage();

    $parts = explode(' | ', $fullErrorMessage);

    expect($parts)->toHaveCount(3)
        ->and($parts[0])->toBe('generic')
        ->and($parts[1])->toBe('SMTP Error')
        ->and($parts[2])->toBe('Connection timeout');
});

it('initializes reason property from payload', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event->reason)
        ->toBeString()
        ->toBe('suppress-bounce');
});

it('initializes delivery status from payload', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event->deliveryStatus)
        ->toBeInstanceOf(HenryAvila\EmailTracking\DataObjects\Mailgun\DeliveryStatus::class)
        ->and($event->deliveryStatus->code)->toBe(605)
        ->and($event->deliveryStatus->description)->toBe('Not delivering to previously bounced address')
        ->and($event->deliveryStatus->attemptNumber)->toBe(1);
});

it('initializes email flags from payload', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event->isRouted)->toBeFalse()
        ->and($event->isAuthenticated)->toBeTrue()
        ->and($event->isSystemTest)->toBeFalse()
        ->and($event->isTestMode)->toBeFalse();
});

it('initializes envelope from payload', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event->envelope)
        ->toBeInstanceOf(HenryAvila\EmailTracking\DataObjects\Mailgun\Envelope::class)
        ->and($event->envelope->sender)->toBe('bob@alertas.crcmg.org.br')
        ->and($event->envelope->transport)->toBe('smtp')
        ->and($event->envelope->targets)->toBe('alice@example.com');
});

it('extends AbstractEmailEvent and inherits its methods', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event)->toBeInstanceOf(HenryAvila\EmailTracking\Events\Email\AbstractEmailEvent::class)
        ->and($event->getMessageId())->toBeString()
        ->and($event->getMessageId())->toBe('20130503192659.13651.20287@alertas.crcmg.org.br')
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBe('example.com');
});

it('has CODE constant set to failed', function () {
    expect(PermanentFailureEmailEvent::CODE)->toBe('failed')
        ->and(TemporaryFailureEmailEvent::CODE)->toBe('failed');
});

it('handles temporary failure with different reason', function () {
    $event = new TemporaryFailureEmailEvent($this->temporaryFailurePayload);

    expect($event->reason)->toBeString()
        ->and($event->deliveryStatus)->not->toBeNull()
        ->and($event->envelope)->not->toBeNull();
});

it('getFullErrorMessage returns only reason when delivery status is empty', function () {
    $payload = $this->permanentFailurePayload;
    $payload['delivery-status']['message'] = null;
    $payload['delivery-status']['description'] = null;
    $payload['reason'] = 'only-reason';

    $event = new PermanentFailureEmailEvent($payload);

    expect($event->getFullErrorMessage())
        ->toBe('only-reason');
});

it('getFullErrorMessage returns only delivery message when reason and description are empty', function () {
    $payload = $this->permanentFailurePayload;
    $payload['delivery-status']['message'] = 'Only delivery message';
    $payload['delivery-status']['description'] = '';
    $payload['reason'] = '';

    $event = new PermanentFailureEmailEvent($payload);

    expect($event->getFullErrorMessage())
        ->toBe('Only delivery message');
});

it('implements HasDeliveryStatus contract', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event)
        ->toBeInstanceOf(HenryAvila\EmailTracking\Contracts\HasDeliveryStatus::class)
        ->and($event->getDeliveryMessage())->toBeString();
});

it('implements HasEmailFlags contract', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event)
        ->toBeInstanceOf(HenryAvila\EmailTracking\Contracts\HasEmailFlags::class)
        ->and($event->isAuthenticated)->toBeBool()
        ->and($event->isRouted)->toBeBool();
});

it('implements HasEnvelope contract', function () {
    $event = new PermanentFailureEmailEvent($this->permanentFailurePayload);

    expect($event)
        ->toBeInstanceOf(HenryAvila\EmailTracking\Contracts\HasEnvelope::class)
        ->and($event->envelope)->not->toBeNull();
});
