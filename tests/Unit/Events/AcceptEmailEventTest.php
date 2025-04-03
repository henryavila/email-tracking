<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Events\Email\AcceptedEmailEvent;

it('creates an "accepted" email event from payload', function () {
    $json = file_get_contents(__DIR__.'/event-data/accepted.json');
    $payload = json_decode($json, true);

    /** @var AcceptedEmailEvent $event */
    $event = HenryAvila\EmailTracking\Factories\EmailEventFactory::make($payload);

    // Verifica propriedades básicas do evento
    expect($event)
        ->toBeInstanceOf(AcceptedEmailEvent::class)
        ->and($event->id)->toBe('nIKIiE5URaSr-8WsuiCrBB')
        ->and($event->timestamp)->toBe('1521472262.908181')
        ->and($event->method)->toBe('HTTP')
        ->and($event->isAuthenticated)->toBeTrue()
        ->and($event->isTestMode)->toBeFalse()
        ->and($event->isRouted)->toBeNull()
        ->and($event->isSystemTest)->toBeNull()
        //
        ->and($event->envelope->transport)->toBe('smtp')
        ->and($event->envelope->sender)->toBe('bob@alertas.crcmg.org.br')
        ->and($event->envelope->targets)->toBe('alice@example.com')
        //
        ->and($event->message->size)->toBe(256)
        ->and($event->message->headers->to)->toBe('Alice <alice@example.com>')
        ->and($event->message->headers->messageId)->toBe('20130503182626.18666.16540@alertas.crcmg.org.br')
        ->and($event->message->headers->from)->toBe('Bob <bob@alertas.crcmg.org.br>')
        ->and($event->message->headers->subject)->toBe('Test accepted webhook')
        //
        ->and($event->recipient)->toBe('alice@example.com')
        ->and($event->recipientDomain)->toBe('example.com')
        ->and($event->tags)->toBe(['my_tag_1', 'my_tag_2']);
});
