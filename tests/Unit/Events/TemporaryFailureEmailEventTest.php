<?php

use HenryAvila\EmailTracking\Events\Email\TemporaryFailureEmailEvent;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;

it('crate a permanent failure email event from payload', function () {

    $payload = json_decode(<<<JSON
{
        "id": "Fs7-5t81S2ispqxqDw2U4Q",
        "timestamp": "1521472262.908181",
        "log-level": "warn",
        "event": "failed",
        "reason": "generic",
        "severity": "temporary",
        "delivery-status": {
            "attempt-no": 1,
            "certificate-verified": true,
            "code": 452,
            "description": "",
            "enhanced-code": "4.2.2",
            "message": "4.2.2 The email account that you tried to reach is over quota. Please direct 4.2.2 the recipient to n4.2.2  https://support.example.com/mail/?p=422",
            "mx-host": "smtp-in.example.com",
            "retry-seconds": 600,
            "session-seconds": 0.1281740665435791,
            "tls": true,
            "utf8": true
        },
        "flags": {
            "is-authenticated": true,
            "is-routed": false,
            "is-system-test": false,
            "is-test-mode": false
        },
        "envelope": {
            "sender": "bob@alertas.crcmg.org.br",
            "transport": "smtp",
            "targets": "alice@example.com",
            "sending-ip": "209.61.154.250"
        },
        "message": {
            "attachments": [],
            "headers": {
                "message-id": "20130503182626.18666.16540@alertas.crcmg.org.br",
                "from": "Bob <bob@alertas.crcmg.org.br>",
                "to": "Alice <alice@example.com>",
                "subject": "Test delivered webhook"
            },
            "size": 111
        },
        "recipient": "alice@example.com",
        "recipient-domain": "example.com",
        "storage": {
            "key": "message_key",
            "url": "https://se.api.mailgun.net/v3/domains/alertas.crcmg.org.br/messages/message_key"
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