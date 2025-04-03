<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\TemporaryFailureEmailEvent;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;

it('crate a "temporary failure" email event from payload', function () {
    $json = file_get_contents(__DIR__ . '/event-data/failed-temporary.json');
    $payload = json_decode($json, true);

    /** @var TemporaryFailureEmailEvent $event */
    $event = EmailEventFactory::make($payload);

    expect($event)
        ->toBeInstanceOf(TemporaryFailureEmailEvent::class)
        ->and($event->id)->toBe('Fs7-5t81S2ispqxqDw2U4Q')
        ->and($event->timestamp)->toBe('1521472262.908181')
        ->and($event->reason)->toBe('generic')
        //
        ->and($event->deliveryStatus->attemptNumber)->toBe(1)
        ->and($event->deliveryStatus->code)->toBe(452)
        ->and($event->deliveryStatus->description)->toBe('')
        ->and($event->deliveryStatus->deliveryMessage)->toBe('4.2.2 The email account that you tried to reach is over quota. Please direct 4.2.2 the recipient to n4.2.2  https://support.example.com/mail/?p=422')
        ->and($event->deliveryStatus->mxHost)->toBe('smtp-in.example.com')
        ->and($event->deliveryStatus->isTls)->toBeTrue()
        ->and($event->deliveryStatus->isUtf8)->toBeTrue()
        ->and($event->getRetrySeconds())->toBe(600)

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
        ->and($event->message->headers->messageId)->toBe('20130503182626.18666.16540@alertas.crcmg.org.br')
        ->and($event->message->headers->from)->toBe('Bob <bob@alertas.crcmg.org.br>')
        ->and($event->message->headers->subject)->toBe('Test delivered webhook')
        //
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBe('example.com');
});
