<?php

use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;

it('creates a delivered email event from payload', closure: function () {
    $payload = [
        "id" => "CPgfbmQMTCKtHW6uIWtuVe",
        "timestamp" => "1521472262.908181",
        "log-level" => "info",
        "event" => "delivered",
        "delivery-status" => [
            "tls" => true,
            "mx-host" => "smtp-in.example.com",
            "code" => 250,
            "description" => "",
            "session-seconds" => 0.4331989288330078,
            "utf8" => true,
            "attempt-no" => 1,
            "message" => "OK",
            "certificate-verified" => true
        ],
        "flags" => [
            "is-routed" => false,
            "is-authenticated" => true,
            "is-system-test" => false,
            "is-test-mode" => false
        ],
        "envelope" => [
            "transport" => "smtp",
            "sender" => "bob@alertas.crcmg.org.br",
            "sending-ip" => "209.61.154.250",
            "targets" => "alice@example.com"
        ],
        "message" => [
            "headers" => [
                "to" => "Alice <alice@example.com>",
                "message-id" => "20130503182626.18666.16540@alertas.crcmg.org.br",
                "from" => "Bob <bob@alertas.crcmg.org.br>",
                "subject" => "Test delivered webhook"
            ],
            "attachments" => [],
            "size" => 111
        ],
        "recipient" => "alice@example.com",
        "recipient-domain" => "example.com",
        "tags" => [
            "my_tag_1",
            "my_tag_2"
        ]
    ];

    /** @var DeliveredEmailEvent $event */
    $event = \HenryAvila\EmailTracking\Factories\EmailEventFactory::make($payload);

    // Verifica propriedades básicas do evento
    expect($event instanceof DeliveredEmailEvent)
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