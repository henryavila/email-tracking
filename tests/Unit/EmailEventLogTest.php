<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Models\Email;
use HenryAvila\EmailTracking\Models\EmailEventLog;

it('can be created with fillable attributes', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'delivered',
        'event_class' => 'DeliveredEmailEvent',
        'payload' => json_encode(['status' => 'success']),
    ]);

    $eventLog->refresh();

    expect($eventLog->email_id)->toBe($email->id)
        ->and($eventLog->event_code)->toBe('delivered')
        ->and($eventLog->event_class)->toBe('DeliveredEmailEvent')
        ->and($eventLog->payload)->toBeArray()
        ->and($eventLog->payload['status'])->toBe('success');
});

it('has email belongsTo relationship', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'opened',
        'event_class' => 'OpenedEmailEvent',
        'payload' => json_encode([]),
    ]);

    expect($eventLog->email())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($eventLog->email)->toBeInstanceOf(Email::class)
        ->and($eventLog->email->id)->toBe($email->id)
        ->and($eventLog->email->message_id)->toBe('test-123');
});

it('decodes JSON payload to array', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $payloadData = [
        'recipient' => 'user@example.com',
        'domain' => 'example.com',
        'ip' => '192.168.1.1',
        'user-agent' => 'Mozilla/5.0',
    ];

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'clicked',
        'event_class' => 'ClickedEmailEvent',
        'payload' => json_encode($payloadData),
    ]);

    $eventLog->refresh();

    expect($eventLog->payload)->toBeArray()
        ->and($eventLog->payload)->toBe($payloadData)
        ->and($eventLog->payload['recipient'])->toBe('user@example.com')
        ->and($eventLog->payload['ip'])->toBe('192.168.1.1');
});

it('handles empty JSON object payload', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'delivered',
        'event_class' => 'DeliveredEmailEvent',
        'payload' => json_encode([]),
    ]);

    $eventLog->refresh();

    expect($eventLog->payload)->toBeArray()
        ->and($eventLog->payload)->toBeEmpty();
});

it('decodes nested JSON payload correctly', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $payloadData = [
        'event' => 'clicked',
        'message' => [
            'headers' => [
                'message-id' => 'test-123',
            ],
        ],
        'recipient' => 'user@example.com',
        'metadata' => [
            'tags' => ['tag1', 'tag2'],
            'variables' => ['var1' => 'value1'],
        ],
    ];

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'clicked',
        'event_class' => 'ClickedEmailEvent',
        'payload' => json_encode($payloadData),
    ]);

    $eventLog->refresh();

    expect($eventLog->payload)->toBeArray()
        ->and($eventLog->payload['message']['headers']['message-id'])->toBe('test-123')
        ->and($eventLog->payload['metadata']['tags'])->toBe(['tag1', 'tag2'])
        ->and($eventLog->payload['metadata']['variables']['var1'])->toBe('value1');
});

it('stores different event types', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $events = [
        ['code' => 'delivered', 'class' => 'DeliveredEmailEvent'],
        ['code' => 'opened', 'class' => 'OpenedEmailEvent'],
        ['code' => 'clicked', 'class' => 'ClickedEmailEvent'],
        ['code' => 'failed', 'class' => 'FailedEmailEvent'],
    ];

    foreach ($events as $event) {
        $eventLog = EmailEventLog::create([
            'email_id' => $email->id,
            'event_code' => $event['code'],
            'event_class' => $event['class'],
            'payload' => json_encode(['type' => $event['code']]),
        ]);

        expect($eventLog->event_code)->toBe($event['code'])
            ->and($eventLog->event_class)->toBe($event['class'])
            ->and($eventLog->payload['type'])->toBe($event['code']);
    }
});

it('maintains relationship after creation', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'delivered',
        'event_class' => 'DeliveredEmailEvent',
        'payload' => json_encode([]),
    ]);

    expect(EmailEventLog::count())->toBe(1)
        ->and($eventLog->email_id)->toBe($email->id)
        ->and($eventLog->email->message_id)->toBe('test-123');
});

it('preserves JSON structure with special characters', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $payloadData = [
        'message' => 'Test with "quotes" and \'apostrophes\'',
        'unicode' => 'Olá! 你好',
        'special' => 'Line\nBreak & Symbols <>&',
    ];

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'delivered',
        'event_class' => 'DeliveredEmailEvent',
        'payload' => json_encode($payloadData),
    ]);

    $eventLog->refresh();

    expect($eventLog->payload)->toBeArray()
        ->and($eventLog->payload['message'])->toContain('quotes')
        ->and($eventLog->payload['unicode'])->toBe('Olá! 你好')
        ->and($eventLog->payload['special'])->toContain('&');
});

it('handles large payload data', function () {
    $email = Email::create([
        'message_id' => 'test-123',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    $largePayload = [];
    for ($i = 0; $i < 100; $i++) {
        $largePayload["key_{$i}"] = "value_{$i}";
    }

    $eventLog = EmailEventLog::create([
        'email_id' => $email->id,
        'event_code' => 'delivered',
        'event_class' => 'DeliveredEmailEvent',
        'payload' => json_encode($largePayload),
    ]);

    $eventLog->refresh();

    expect($eventLog->payload)->toBeArray()
        ->and($eventLog->payload)->toHaveCount(100)
        ->and($eventLog->payload['key_0'])->toBe('value_0')
        ->and($eventLog->payload['key_99'])->toBe('value_99');
});
