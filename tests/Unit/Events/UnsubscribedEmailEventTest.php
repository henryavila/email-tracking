<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\UnsubscribeEmailEvent;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;

it('create a "unsubscribed" email event from payload', function () {
    $payload = json_decode(<<<'JSON'
 {
        "id": "Ase7i2zsRYeDXztHGENqRA",
        "timestamp": "1521243339.873676",
        "log-level": "info",
        "event": "unsubscribed",
        "message": {
            "headers": {
                "message-id": "20130503182626.18666.16540@alertas.crcmg.org.br"
            }
        },
        "recipient": "alice@example.com",
        "recipient-domain": "example.com",
        "ip": "50.56.129.169",
        "geolocation": {
            "country": "US",
            "region": "CA",
            "city": "San Francisco"
        },
        "client-info": {
            "client-os": "Linux",
            "device-type": "desktop",
            "client-name": "Chrome",
            "client-type": "browser",
            "user-agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31"
        },
        "campaigns": [],
        "tags": [
            "my_tag_1",
            "my_tag_2"
        ],
        "user-variables": {
            "my_var_1": "Mailgun Variable #1",
            "my-var-2": "awesome"
        }
    }
JSON, true
    );

    /** @var UnsubscribeEmailEvent $event */
    $event = EmailEventFactory::make($payload);

    expect($event)
        ->toBeInstanceOf(UnsubscribeEmailEvent::class)
        ->and($event->id)->toBe('Ase7i2zsRYeDXztHGENqRA')
        ->and($event->timestamp)->toBe('1521243339.873676')
        ->and($event->message->headers->messageId)->toBe('20130503182626.18666.16540@alertas.crcmg.org.br')
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBe('example.com')
        ->and($event->ip)->toBe('50.56.129.169')
        ->and($event->geolocation->country)->toBe('US')
        ->and($event->geolocation->region)->toBe('CA')
        ->and($event->geolocation->city)->toBe('San Francisco')
        ->and($event->clientInfo->clientOs)->toBe('Linux')
        ->and($event->clientInfo->deviceType)->toBe('desktop')
        ->and($event->clientInfo->clientName)->toBe('Chrome')
        ->and($event->clientInfo->clientType)->toBe('browser')
        ->and($event->clientInfo->userAgent)->toBe('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31');
});
