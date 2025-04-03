<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;

it('creates a "delivered" email event from payload', closure: function () {
    $json = file_get_contents(__DIR__.'/event-data/delivered.json');
    $payload = json_decode($json, true);

    /** @var DeliveredEmailEvent $event */
    $event = HenryAvila\EmailTracking\Factories\EmailEventFactory::make($payload);

    // Verifica propriedades básicas do evento
    expect($event)
        ->toBeInstanceOf(DeliveredEmailEvent::class)
        ->and($event->id)->toBe('CPgfbmQMTCKtHW6uIWtuVe')
        ->and($event->timestamp)->toBe('1521472262.908181')
        //
        ->and($event->deliveryStatus->isTls)->toBeTrue()
        ->and($event->deliveryStatus->mxHost)->toBe('smtp-in.example.com')
        ->and($event->deliveryStatus->code)->toBe(250)
        ->and($event->deliveryStatus->description)->toBe('')
        ->and($event->deliveryStatus->isUtf8)->toBeTrue()
        ->and($event->deliveryStatus->attemptNumber)->toBe(1)
        ->and($event->deliveryStatus->deliveryMessage)->toBe('OK')
        //
        ->and($event->isRouted)->toBeFalse()
        ->and($event->isAuthenticated)->toBeTrue()
        ->and($event->isSystemTest)->toBeFalse()
        ->and($event->isTestMode)->toBeFalse()
        //
        ->and($event->envelope->transport)->toBe('smtp')
        ->and($event->envelope->sender)->toBe('bob@alertas.crcmg.org.br')
        ->and($event->envelope->sendingIp)->toBe('209.61.154.250')
        ->and($event->envelope->targets)->toBe('alice@example.com')
        //
        ->and($event->message->headers->to)->toBe('Alice <alice@example.com>')
        ->and($event->message->headers->messageId)->toBe('20130503182626.18666.16540@alertas.crcmg.org.br')
        ->and($event->message->headers->from)->toBe('Bob <bob@alertas.crcmg.org.br>')
        ->and($event->message->headers->subject)->toBe('Test delivered webhook')
        ->and($event->message->size)->toBe(111)
        //
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBe('example.com')
        ->and($event->campaigns)->toBe([])
        ->and($event->tags)->toBe(['my_tag_1', 'my_tag_2']);

});
