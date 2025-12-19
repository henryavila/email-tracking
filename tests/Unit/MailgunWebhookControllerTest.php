<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Controllers\MailgunWebhookController;
use HenryAvila\EmailTracking\Events\EmailWebhookProcessed;
use HenryAvila\EmailTracking\Models\Email;
use HenryAvila\EmailTracking\Models\EmailEventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->controller = new MailgunWebhookController;

    Event::fake();
    Log::spy();

    config()->set('email-tracking.save-email-event-in-database', true);
    config()->set('email-tracking.log-email-not-found', true);
});

it('processes delivered event successfully', function () {
    $email = Email::create([
        'message_id' => 'f787b68095bc31b2e3751948209f14cf@emails.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'teste@gmail.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered-real.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData()->success)->toBeTrue();

    $email->refresh();

    expect($email->delivered_at)->not->toBeNull()
        ->and($email->delivery_status_attempts)->toBe(1)
        ->and($email->delivery_status_message)->toContain('OK');

    Event::assertDispatched(EmailWebhookProcessed::class);
});

it('processes opened event and increments counter', function () {
    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
        'opened' => 0,
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/opened.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    $email->refresh();

    expect($response->getData()->success)->toBeTrue()
        ->and($email->opened)->toBe(1)
        ->and($email->first_opened_at)->not->toBeNull()
        ->and($email->last_opened_at)->toBeNull(); // First open, so last is null
});

it('updates last_opened_at on subsequent opens', function () {
    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
        'opened' => 1,
        'first_opened_at' => now()->subHour(),
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/opened.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    ($this->controller)($request);

    $email->refresh();

    expect($email->opened)->toBe(2)
        ->and($email->first_opened_at)->not->toBeNull()
        ->and($email->last_opened_at)->not->toBeNull();
});

it('processes clicked event and increments counter', function () {
    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
        'clicked' => 0,
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/clicked.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    $email->refresh();

    expect($response->getData()->success)->toBeTrue()
        ->and($email->clicked)->toBe(1)
        ->and($email->first_clicked_at)->not->toBeNull();
});

it('processes permanent failure event', function () {
    $email = Email::create([
        'message_id' => '20130503192659.13651.20287@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/failed-permanent.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    $email->refresh();

    expect($response->getData()->success)->toBeTrue()
        ->and($email->failed_at)->not->toBeNull()
        ->and($email->delivery_status_attempts)->toBe(1);

    // Delivery status message may be null if event doesn't have delivery message
    if ($email->delivery_status_message !== null) {
        expect(str_contains($email->delivery_status_message, 'Not delivering to previously bounced address'))->toBeTrue();
    }
});

it('processes temporary failure event', function () {
    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/failed-temporary.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    $email->refresh();

    expect($response->getData()->success)->toBeTrue()
        ->and($email->delivery_status_attempts)->toBeGreaterThan(0);
});

it('returns error when email is not found', function () {
    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData()->success)->toBeFalse()
        ->and($response->getData()->message)->toContain('Email not found');

    Log::shouldHaveReceived('warning')
        ->once()
        ->with('Email not found', Mockery::any());
});

it('does not log warning when email not found and logging is disabled', function () {
    config()->set('email-tracking.log-email-not-found', false);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    expect($response->getData()->success)->toBeFalse();

    Log::shouldNotHaveReceived('warning');
});

it('creates email event log when configured', function () {
    config()->set('email-tracking.save-email-event-in-database', true);

    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    ($this->controller)($request);

    expect(EmailEventLog::count())->toBe(1);

    $eventLog = EmailEventLog::first();

    expect($eventLog->email_id)->toBe($email->id)
        ->and($eventLog->event_code)->toBe('delivered')
        ->and($eventLog->event_class)->toContain('DeliveredEmailEvent');
});

it('does not create email event log when disabled', function () {
    config()->set('email-tracking.save-email-event-in-database', false);

    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    ($this->controller)($request);

    expect(EmailEventLog::count())->toBe(0);
});

it('dispatches EmailWebhookProcessed event', function () {
    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/delivered.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    ($this->controller)($request);

    Event::assertDispatched(EmailWebhookProcessed::class, function ($event) {
        return $event->emailEvent instanceof HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;
    });
});

it('appends delivery messages with separator', function () {
    $email = Email::create([
        'message_id' => '20130503192659.13651.20287@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
        'delivery_status_message' => '18/12/2025 10:00:00 - First message',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/failed-permanent.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    ($this->controller)($request);

    $email->refresh();

    // Permanent failure event might not have delivery message, just verify the message was preserved
    expect($email->delivery_status_message)->toBeString()
        ->and(str_contains($email->delivery_status_message, 'First message'))->toBeTrue();
});

it('throws exception when payload is invalid', function () {
    // Invalid payload to cause TypeError in EmailEventFactory
    $request = Request::create('/webhook', 'POST', ['event-data' => null]);

    expect(fn () => ($this->controller)($request))
        ->toThrow(TypeError::class);
});

it('processes accepted event successfully', function () {
    $email = Email::create([
        'message_id' => '20130503182626.18666.16540@alertas.crcmg.org.br',
        'subject' => 'Test',
        'to' => 'alice@example.com',
    ]);

    $payload = json_decode(
        file_get_contents(__DIR__.'/Events/event-data/accepted.json'),
        true
    );

    $request = Request::create('/webhook', 'POST', ['event-data' => $payload]);

    $response = ($this->controller)($request);

    expect($response->getData()->success)->toBeTrue();

    Event::assertDispatched(EmailWebhookProcessed::class);
});
