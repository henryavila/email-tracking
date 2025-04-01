<?php

use HenryAvila\EmailTracking\Events\Email\ClickedEmailEvent;

it('creates a clicked email event from payload', function () {
    $payload = [
        "id" => "Ase7i2zsRYeDXztHGENqRA",
        "timestamp" => "1521243339.873676",
        "log-level" => "info",
        "event" => "clicked",
        "message" => [
            "headers" => [
                "message-id" => "20130503182626.18666.16540@alertas.crcmg.org.br"
            ]
        ],
        "recipient" => "alice@example.com",
        "recipient-domain" => "example.com",
        "ip" => "50.56.129.169",
        "geolocation" => [
            "country" => "US",
            "region" => "CA",
            "city" => "San Francisco"
        ],
        "client-info" => [
            "client-os" => "Linux",
            "device-type" => "desktop",
            "client-name" => "Chrome",
            "client-type" => "browser",
            "user-agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31"
        ],
        "campaigns" => [],
        "tags" => [
            "my_tag_1",
            "my_tag_2"
        ]
    ];

    /** @var ClickedEmailEvent $event */
    $event = \HenryAvila\EmailTracking\Factories\EmailEventFactory::make($payload);

    // Verifica propriedades básicas do evento
    expect($event instanceof ClickedEmailEvent)
        ->and($event->id)->toBe('Ase7i2zsRYeDXztHGENqRA')
        ->and($event->timestamp)->toBe('1521243339.873676')
        ->and($event->recipient)->toBe('alice@example.com')
        //
        ->and($event->message->headers->messageId)->toBe('20130503182626.18666.16540@alertas.crcmg.org.br')
        //
        ->and($event->recipientDomain)->toBe('example.com')
        ->and($event->tags)->toBe(['my_tag_1', 'my_tag_2'])
        ->and($event->ip)->toBe('50.56.129.169')
        //
        ->and($event->clientInfo->clientOs)->toBe('Linux')
        ->and($event->clientInfo->deviceType)->toBe('desktop')
        ->and($event->clientInfo->clientName)->toBe('Chrome')
        ->and($event->clientInfo->clientType)->toBe('browser')
        ->and($event->clientInfo->userAgent)->toBe('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31')
        //
        ->and($event->geolocation->country)->toBe('US')
        ->and($event->geolocation->region)->toBe('CA')
        ->and($event->geolocation->city)->toBe('San Francisco');
});