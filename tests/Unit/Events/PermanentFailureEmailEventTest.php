<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\PermanentFailureEmailEvent;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;

it('crate a "permanent failure" email event from payload', function () {
    $json = file_get_contents(__DIR__.'/event-data/failed-permanent.json');
    $payload = json_decode($json, true);

    /** @var PermanentFailureEmailEvent $event */
    $event = EmailEventFactory::make($payload);

    expect($event)
        ->toBeInstanceOf(PermanentFailureEmailEvent::class)
        ->and($event->id)->toBe('G9Bn5sl1TC6nu79C8C0bwg')
        ->and($event->timestamp)->toBe('1521233195.375624')
        ->and($event->reason)->toBe('suppress-bounce')
        //
        ->and($event->deliveryStatus->attemptNumber)->toBe(1)
        ->and($event->deliveryStatus->deliveryMessage)->toBe('')
        ->and($event->deliveryStatus->code)->toBe(605)
        ->and($event->deliveryStatus->description)->toBe('Not delivering to previously bounced address')
        //
        ->and($event->isRouted)->toBeFalse()
        ->and($event->isAuthenticated)->toBeTrue()
        ->and($event->isSystemTest)->toBeFalse()
        ->and($event->isTestMode)->toBeFalse()
        //
        ->and($event->envelope->sender)->toBe('bob@alertas.crcmg.org.br')
        ->and($event->envelope->transport)->toBe('smtp')
        ->and($event->envelope->targets)->toBe('alice@example.com')
        //
        ->and($event->message->headers->to)->toBe('Alice <alice@example.com>')
        ->and($event->message->headers->messageId)->toBe('20130503192659.13651.20287@alertas.crcmg.org.br')
        ->and($event->message->headers->from)->toBe('Bob <bob@alertas.crcmg.org.br>')
        ->and($event->message->headers->subject)->toBe('Test permanent_fail webhook')
        //
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBe('example.com');
});
