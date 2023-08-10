<?php

use HenryAvila\EmailTracking\Listeners\LogEmailSentListener;
use HenryAvila\EmailTracking\Mail\TrackableMail;
use HenryAvila\EmailTracking\Models\Email;
use HenryAvila\EmailTracking\Models\User;
use HenryAvila\EmailTracking\Notifications\SampleNotification;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

test('Set Model Connection', function () {
    $email = new Email();
    assertNull($email->getConnectionName());

    $connectionName = 'log';
    config()->set('email-tracking.email-db-connection', $connectionName);
    $email = new Email();
    assertEquals($connectionName, $email->getConnectionName());
});

it('can send Custom Mail passing model data', function () {
    copyViewFiles();
    $user = User::factory()->create();

    Event::fake([
        MessageSending::class,
        MessageSent::class,
    ]);


    $mailable = (new TrackableMail($user, 'emails.sample'))->to($user->email)->from($user->email);
    Mail::send($mailable);

    $mailable->assertSeeInOrderInHtml(['HTML', $user->name]);

    Event::assertDispatched(MessageSending::class, function (MessageSending $event) use ($user) {
        assertNotEmpty($event->data['model']);
        assertEquals($event->data['model']?->id, $user->id);

        return true;
    });
    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        assertNotEmpty($event->data['model']);
        assertEquals($event->data['model']?->id, $user->id);

        return true;
    });
    Event::assertDispatched(MessageSending::class);
    Event::assertDispatched(MessageSent::class);

    Mail::fake();
    Mail::assertNothingSent();
    Mail::send($mailable);
    Mail::assertQueued(TrackableMail::class);
});

it('create a email object on custom Mailable send', function () {
    $user = User::factory()->create();
    Event::fake([
        MessageSending::class,
        MessageSent::class,
    ]);

    assertDatabaseCount((new Email())->getTable(), 0);

    $mailable = (new TrackableMail($user, 'emails.sample'))->to($user->email)->from($user->email);
    Mail::send($mailable);

    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        assertNotEmpty($event->data['model']);
        assertEquals($event->data['model']?->id, $user->id);


        $listener = new LogEmailSentListener();
        $listener->handle($event);

        assertDatabaseCount((new Email())->getTable(), 1);
        assertDatabaseHas((new Email())->getTable(), [
            'id' => 1,
        ]);
        $mailLog = Email::find(1);

        assertEquals($mailLog->to, $user->email);
        assertEquals(User::class, $mailLog->sender_type);
        assertEquals($user->id, $mailLog->sender_id);

        return true;
    });
});


it('can send Custom Notification passing model data', function () {
    copyViewFiles();
    $user = User::factory()->create();

    Event::fake([
        MessageSending::class,
        MessageSent::class,
    ]);

    Notification::route('mail', $user->email)
        ->notify(new SampleNotification($user));


    Event::assertDispatched(MessageSending::class, function (MessageSending $event) use ($user) {
        assertNotEmpty($event->data['model']);
        assertEquals($event->data['model']?->id, $user->id);

        return true;
    });
    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        assertNotEmpty($event->data['model']);
        assertEquals($event->data['model']?->id, $user->id);

        return true;
    });
    Event::assertDispatched(MessageSending::class);
    Event::assertDispatched(MessageSent::class);
});

it('create a email object on custom Notification send', function () {
    $user = User::factory()->create();
    Event::fake([
        MessageSending::class,
        MessageSent::class,
    ]);

    assertDatabaseCount((new Email())->getTable(), 0);

    Notification::route('mail', $user->email)
        ->notify(new SampleNotification($user));

    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        assertNotEmpty($event->data['model']);
        assertEquals($event->data['model']?->id, $user->id);

        $listener = new LogEmailSentListener();
        $listener->handle($event);

        assertDatabaseCount((new Email())->getTable(), 1);
        assertDatabaseHas((new Email())->getTable(), [
            'id' => 1,
        ]);
        $mailLog = Email::find(1);

        assertEquals($mailLog->to, $user->email);
        assertEquals(User::class, $mailLog->sender_type);
        assertEquals($user->id, $mailLog->sender_id);

        return true;
    });
});


it('can handle mailgun webhook on DELIVERED status', function () {
    $user = User::factory()->create();
    Event::fake([MessageSent::class]);

    $mailable = (new TrackableMail($user, 'emails.sample'))->to($user->email)->from($user->email);
    Mail::send($mailable);

    // Handle MessageSent event
    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        $listener = new LogEmailSentListener();
        $listener->handle($event);

        return true;
    });

    /** @var Email $emailLog */
    $emailLog = Email::first();


    $mailGunRequestData = getMailGunRequestData($emailLog, 'delivered');

    $this->post(route('email-tracking.webhooks.mailgun'), $mailGunRequestData);
    assertDatabaseCount((new Email())->getTable(), 1);

    $emailLog = Email::first();

    assertNotNull($emailLog->delivered_at);
    assertNull($emailLog->failed_at);
    assertEquals(0, $emailLog->opened);
    assertEquals(0, $emailLog->clicked);
    assertEquals(1, $emailLog->delivery_status_attempts);
    assertTrue(str_contains($emailLog->delivery_status_message, 'OK'));
    assertNull($emailLog->first_opened_at);
    assertNull($emailLog->first_clicked_at);
    assertNull($emailLog->last_opened_at);
    assertNull($emailLog->last_clicked_at);
});


it('can handle mailgun webhook on OPENED status', function () {
    $user = User::factory()->create();
    Event::fake([MessageSent::class]);

    $mailable = (new TrackableMail($user, 'emails.sample'))->to($user->email)->from($user->email);
    Mail::send($mailable);

    // Handle MessageSent event
    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        $listener = new LogEmailSentListener();
        $listener->handle($event);

        return true;
    });

    /** @var Email $emailLog */
    $emailLog = Email::first();


    $mailGunRequestData = getMailGunRequestData($emailLog, 'opened');

    $this->post(route('email-tracking.webhooks.mailgun'), $mailGunRequestData);
    assertDatabaseCount((new Email())->getTable(), 1);

    $emailLog = Email::first();

    assertNull($emailLog->delivered_at);
    assertNull($emailLog->failed_at);
    assertEquals(1, $emailLog->opened);
    assertEquals(0, $emailLog->clicked);
    assertNull($emailLog->delivery_status_attempts);
    assertNull($emailLog->delivery_status_message);
    assertNotNull($emailLog->first_opened_at);
    assertNull($emailLog->first_clicked_at);
    assertNull($emailLog->last_opened_at);
    assertNull($emailLog->last_clicked_at);
});


it('can handle mailgun webhook on CLICKED status', function () {
    $user = User::factory()->create();
    Event::fake([MessageSent::class]);

    $mailable = (new TrackableMail($user, 'emails.sample'))->to($user->email)->from($user->email);
    Mail::send($mailable);

    // Handle MessageSent event
    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        $listener = new LogEmailSentListener();
        $listener->handle($event);

        return true;
    });

    /** @var Email $emailLog */
    $emailLog = Email::first();


    $mailGunRequestData = getMailGunRequestData($emailLog, 'clicked');

    $this->post(route('email-tracking.webhooks.mailgun'), $mailGunRequestData);
    assertDatabaseCount((new Email())->getTable(), 1);

    $emailLog = Email::first();

    assertNull($emailLog->delivered_at);
    assertNull($emailLog->failed_at);
    assertEquals(0, $emailLog->opened);
    assertEquals(1, $emailLog->clicked);
    assertNull($emailLog->delivery_status_attempts);
    assertNull($emailLog->delivery_status_message);
    assertNull($emailLog->first_opened_at);
    assertNotNull($emailLog->first_clicked_at);
    assertNull($emailLog->last_opened_at);
    assertNull($emailLog->last_clicked_at);
});

/**
 * @param string $event delivered, clicked, opened
 */
function getMailGunRequestData(Email $emailLog, string $event): array
{
    $timestamp = now()->timestamp;
    $token = "999999999999999999999999999999999999999999";

    $baseData = [
        "signature" => [
            "token" => $token,
            "timestamp" => $timestamp,
            "signature" => hash_hmac('sha256', $timestamp . $token, config('services.mailgun.secret')),
        ],
        "event-data" => [],
    ];

    switch ($event) {
        case 'delivered':
            $baseData['event-data'] = [
                "event" => "delivered",
                "message" => [
                    "headers" => [
                        "to" => $emailLog->to,
                        "message-id" => $emailLog->message_id,
                        "from" => $emailLog->subject,
                        "subject" => "message subject",
                    ],
                ],
                "delivery-status" => [
                    "tls" => true,
                    "mx-host" => "mx.gmail.com",
                    "code" => 250,
                    "description" => "",
                    "session-seconds" => 56.981908082962,
                    "attempt-no" => 1,
                    "message" => "OK",
                ],
            ];

            break;

        case 'clicked':
            $baseData['event-data'] = [
                "event" => "clicked",
                "geolocation" => [
                    "country" => "US",
                    "region" => "Unknown",
                    "city" => "Unknown",
                ],
                "tags" => [
                ],
                "url" => "https://sample.amazonaws.com/999999999999999999999999999.pdf",
                "ip" => "1.1.1.1",
                "log-level" => "info",
                "timestamp" => 1651584901.9819,
                "client-info" => [
                    "client-name" => "Chrome",
                    "client-type" => "browser",
                    "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36",
                    "device-type" => "desktop",
                    "client-os" => "Windows",
                ],
                "message" => [
                    "headers" => [
                        "message-id" => $emailLog->message_id,
                    ],
                ],
                "recipient" => $emailLog->to,
            ];

            break;

        case 'opened':
            $baseData['event-data'] = [
                "event" => "opened",
                "geolocation" => [
                    "country" => "US",
                    "region" => "Unknown",
                    "city" => "Unknown",
                ],
                "ip" => "1.1.1.1",
                "recipient-domain" => $emailLog->to,
                "id" => "9999999999999999999",
                "log-level" => "info",
                "timestamp" => 1651584876.2409,
                "client-info" => [
                    "client-name" => "Firefox",
                    "client-type" => "browser",
                    "user-agent" => "Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko Firefox/11.0 (via ggpht.com GoogleImageProxy)",
                    "device-type" => "desktop",
                    "client-os" => "Windows",
                ],
                "message" => [
                    "headers" => [
                        "message-id" => $emailLog->message_id,
                    ],
                ],
                "recipient" => $emailLog->to,
            ];

            break;
    }

    return $baseData;
}


/**
 * Hack to make the view work on Email test
 */
function copyViewFiles(): void
{
    $ds = DIRECTORY_SEPARATOR;
    shell_exec(
        "cp -r " .
        __DIR__ . "{$ds}..{$ds}resources{$ds}views{$ds}emails " .
        __DIR__ . "{$ds}..{$ds}vendor{$ds}orchestra{$ds}testbench-core{$ds}laravel{$ds}resources{$ds}views"
    );
}
