<?php

use HenryAvila\EmailTracking\Events\Email\PermanentFailureEmailEvent;
use HenryAvila\EmailTracking\Factories\EmailEventFactory;

it('crate a "permanent failure" email event from payload', function () {
    $payload = json_decode(<<<JSON
{
        "id": "G9Bn5sl1TC6nu79C8C0bwg",
        "timestamp": "1521233195.375624",
        "log-level": "error",
        "event": "failed",
        "severity": "permanent",
        "reason": "suppress-bounce",
        "delivery-status": {
            "attempt-no": 1,
            "message": "",
            "code": 605,
            "enhanced-code": "",
            "description": "Not delivering to previously bounced address",
            "session-seconds": 0
        },
        "flags": {
            "is-routed": false,
            "is-authenticated": true,
            "is-system-test": false,
            "is-test-mode": false
        },
        "envelope": {
            "sender": "bob@alertas.crcmg.org.br",
            "transport": "smtp",
            "targets": "alice@example.com"
        },
        "message": {
            "headers": {
                "to": "Alice <alice@example.com>",
                "message-id": "20130503192659.13651.20287@alertas.crcmg.org.br",
                "from": "Bob <bob@alertas.crcmg.org.br>",
                "subject": "Test permanent_fail webhook"
            },
            "attachments": [],
            "size": 111
        },
        "recipient": "alice@example.com",
        "recipient-domain": "example.com",
        "storage": {
            "url": "https://se.api.mailgun.net/v3/domains/alertas.crcmg.org.br/messages/message_key",
            "key": "message_key"
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