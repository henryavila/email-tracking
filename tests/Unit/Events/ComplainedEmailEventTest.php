<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\SpamComplaintsEmailEvent;

it('creates a "complained" email event from payload', function () {
    $json = file_get_contents(__DIR__ . '/event-data/complained.json');
    $payload = json_decode($json, true);

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
