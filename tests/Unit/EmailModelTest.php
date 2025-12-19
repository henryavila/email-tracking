<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Models\Email;
use HenryAvila\EmailTracking\Models\User;

it('can be created with fillable attributes', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test Subject',
        'to' => 'test@example.com',
        'email_type' => 'transactional',
    ]);

    expect($email->message_id)->toBe('test-123')
        ->and($email->subject)->toBe('Test Subject')
        ->and($email->to)->toBe('test@example.com')
        ->and($email->email_type)->toBe('transactional');
});

it('casts delivered_at to datetime', function () {
    $email = Email::create([
        'message_id' => 'test',
        'delivered_at' => '2025-12-19 10:00:00',
    ]);

    expect($email->delivered_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($email->delivered_at->format('Y-m-d H:i:s'))->toBe('2025-12-19 10:00:00');
});

it('casts opened and clicked to integer', function () {
    $email = Email::create([
        'message_id' => 'test',
        'opened' => '5',
        'clicked' => '3',
    ]);

    expect($email->opened)->toBeInt()
        ->and($email->opened)->toBe(5)
        ->and($email->clicked)->toBeInt()
        ->and($email->clicked)->toBe(3);
});

it('has sender morphTo relationship', function () {
    $user = User::factory()->create();

    $email = Email::create([
        'message_id' => 'test',
        'sender_type' => User::class,
        'sender_id' => $user->id,
    ]);

    expect($email->sender())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\MorphTo::class)
        ->and($email->sender)->toBeInstanceOf(User::class)
        ->and($email->sender->id)->toBe($user->id);
});

it('has emailEventLogs hasMany relationship', function () {
    $email = Email::create(['message_id' => 'test']);

    expect($email->emailEventLogs())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('uses custom database connection when configured', function () {
    config()->set('email-tracking.email-db-connection', 'custom_connection');

    $email = new Email;

    expect($email->getConnectionName())->toBe('custom_connection');
});

it('uses default connection when not configured', function () {
    config()->set('email-tracking.email-db-connection', null);

    $email = new Email;

    expect($email->getConnectionName())->toBeNull();
});

it('stores all tracking timestamps correctly', function () {
    $email = Email::create([
        'message_id' => 'test',
        'first_opened_at' => '2025-12-19 10:00:00',
        'last_opened_at' => '2025-12-19 11:00:00',
        'first_clicked_at' => '2025-12-19 10:30:00',
        'last_clicked_at' => '2025-12-19 11:30:00',
    ]);

    expect($email->first_opened_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($email->last_opened_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($email->first_clicked_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($email->last_clicked_at)->toBeInstanceOf(Carbon\Carbon::class);
});

it('stores delivery status information', function () {
    $email = Email::create([
        'message_id' => 'test',
        'delivery_status_attempts' => 3,
        'delivery_status_message' => 'Delivered successfully',
    ]);

    expect($email->delivery_status_attempts)->toBe(3)
        ->and($email->delivery_status_message)->toBe('Delivered successfully');
});

it('stores HTML and text body content', function () {
    $email = Email::create([
        'message_id' => 'test',
        'body_html' => '<p>HTML content</p>',
        'body_txt' => 'Plain text content',
    ]);

    expect($email->body_html)->toBe('<p>HTML content</p>')
        ->and($email->body_txt)->toBe('Plain text content');
});

it('stores recipient information', function () {
    $email = Email::create([
        'message_id' => 'test',
        'to' => 'recipient@example.com',
        'cc' => 'cc@example.com',
        'bcc' => 'bcc@example.com',
        'reply_to' => 'reply@example.com',
    ]);

    expect($email->to)->toBe('recipient@example.com')
        ->and($email->cc)->toBe('cc@example.com')
        ->and($email->bcc)->toBe('bcc@example.com')
        ->and($email->reply_to)->toBe('reply@example.com');
});
