<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\SpamComplaintsEmailEvent;

it('creates a "complained" email event from payload', function () {
    $payload = [
        'id' => '-Agny091SquKnsrW2NEKUA',
        'timestamp' => '1521233123.501324',
        'log-level' => 'warn',
        'event' => 'complained',
        'envelope' => [
            'sending-ip' => '173.193.210.33',
        ],
        'flags' => [
            'is-test-mode' => false,
        ],
        'message' => [
            'headers' => [
                'to' => 'Alice <alice@example.com>',
                'message-id' => '20110215055645.25246.63817@alertas.crcmg.org.br',
                'from' => 'Bob <bob@alertas.crcmg.org.br>',
                'subject' => 'Test complained webhook',
            ],
            'attachments' => [],
            'size' => 111,
        ],
        'recipient' => 'alice@example.com',
        'campaigns' => [],
        'tags' => [
            'my_tag_1',
            'my_tag_2',
        ],
    ];

    /** @var SpamComplaintsEmailEvent $event */
    $event = HenryAvila\EmailTracking\Factories\EmailEventFactory::make($payload);

    // Verifica propriedades básicas do evento
    expect($event)
        ->toBeInstanceOf(SpamComplaintsEmailEvent::class)
        ->and($event->id)->toBe('-Agny091SquKnsrW2NEKUA')
        ->and($event->timestamp)->toBe('1521233123.501324')
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->tags)->toBe(['my_tag_1', 'my_tag_2'])
        //
        ->and($event->envelope->sendingIp)->toBe('173.193.210.33')
        //
        ->and($event->message->headers->to)->toBe('Alice <alice@example.com>')
        ->and($event->message->headers->from)->toBe('Bob <bob@alertas.crcmg.org.br>')
        ->and($event->message->headers->subject)->toBe('Test complained webhook')
        ->and($event->message->headers->messageId)->toBe('20110215055645.25246.63817@alertas.crcmg.org.br')
        ->and($event->message->size)->toBe(111);
});
