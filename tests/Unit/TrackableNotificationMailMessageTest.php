<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Models\User;
use HenryAvila\EmailTracking\Notifications\TrackableNotificationMailMessage;
use Illuminate\Support\HtmlString;

// No beforeEach needed - migrations are loaded automatically via TestCase

it('can be instantiated with a model', function () {
    $user = User::factory()->create();
    $message = new TrackableNotificationMailMessage($user);

    expect($message->model)->toBe($user)
        ->and($message)->toBeInstanceOf(TrackableNotificationMailMessage::class);
});

it('can be instantiated without a model', function () {
    $message = new TrackableNotificationMailMessage;

    expect($message->model)->toBeNull();
});

it('includes model in toArray when model is set', function () {
    $user = User::factory()->create();
    $message = new TrackableNotificationMailMessage($user);
    $message->subject('Test Subject');

    $array = $message->toArray();

    expect($array)->toHaveKey('model')
        ->and($array['model'])->toBe($user)
        ->and($array)->toHaveKey('subject')
        ->and($array['subject'])->toBe('Test Subject');
});

it('does not include model in toArray when model is null', function () {
    $message = new TrackableNotificationMailMessage;
    $message->subject('Test Subject');

    $array = $message->toArray();

    expect($array)->not->toHaveKey('model')
        ->and($array)->toHaveKey('subject');
});

it('adds a single blank line', function () {
    $message = new TrackableNotificationMailMessage;
    $result = $message->blankLine();

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(1)
        ->and($message->introLines[0])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[0]->toHtml())->toBe('<p></p><br />');
});

it('adds multiple blank lines', function () {
    $message = new TrackableNotificationMailMessage;
    $result = $message->blankLine(3);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(3)
        ->and($message->introLines[0])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[1])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[2])->toBeInstanceOf(HtmlString::class);
});

it('adds blank line when condition is true', function () {
    $message = new TrackableNotificationMailMessage;
    $result = $message->blankLineIf(true);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(1)
        ->and($message->introLines[0])->toBeInstanceOf(HtmlString::class);
});

it('does not add blank line when condition is false', function () {
    $message = new TrackableNotificationMailMessage;
    $result = $message->blankLineIf(false);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(0);
});

it('adds html line', function () {
    $message = new TrackableNotificationMailMessage;
    $htmlContent = '<strong>Bold text</strong>';
    $result = $message->htmlLine($htmlContent);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(1)
        ->and($message->introLines[0])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[0]->toHtml())->toBe($htmlContent);
});

it('adds html line when condition is true', function () {
    $message = new TrackableNotificationMailMessage;
    $htmlContent = '<em>Italic text</em>';
    $result = $message->htmlLineIf(true, $htmlContent);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(1)
        ->and($message->introLines[0])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[0]->toHtml())->toBe($htmlContent);
});

it('does not add html line when condition is false', function () {
    $message = new TrackableNotificationMailMessage;
    $htmlContent = '<em>Italic text</em>';
    $result = $message->htmlLineIf(false, $htmlContent);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(0);
});

it('can chain multiple methods', function () {
    $user = User::factory()->create();
    $message = new TrackableNotificationMailMessage($user);

    $result = $message
        ->subject('Chained Test')
        ->greeting('Hello!')
        ->line('Regular line')
        ->htmlLine('<strong>HTML line</strong>')
        ->blankLine()
        ->blankLineIf(true)
        ->htmlLineIf(true, '<em>Conditional HTML</em>')
        ->line('Final line');

    expect($result)->toBe($message)
        ->and($message->subject)->toBe('Chained Test')
        ->and($message->greeting)->toBe('Hello!')
        ->and($message->introLines)->toHaveCount(6);
});

it('preserves model through method chaining', function () {
    $user = User::factory()->create();
    $message = new TrackableNotificationMailMessage($user);

    $message
        ->subject('Test')
        ->line('Line 1')
        ->blankLine()
        ->htmlLine('<p>HTML</p>');

    $array = $message->toArray();

    expect($array)->toHaveKey('model')
        ->and($array['model'])->toBe($user)
        ->and($message->model)->toBe($user);
});

it('works with different html content', function () {
    $message = new TrackableNotificationMailMessage;

    $message
        ->htmlLine('<div class="alert">Alert!</div>')
        ->htmlLine('<a href="https://example.com">Link</a>')
        ->htmlLine('<ul><li>Item 1</li><li>Item 2</li></ul>');

    expect($message->introLines)->toHaveCount(3)
        ->and($message->introLines[0]->toHtml())->toContain('alert')
        ->and($message->introLines[1]->toHtml())->toContain('href')
        ->and($message->introLines[2]->toHtml())->toContain('<ul>');
});

it('can add blank lines with zero count', function () {
    $message = new TrackableNotificationMailMessage;
    $result = $message->blankLine(0);

    expect($result)->toBe($message)
        ->and($message->introLines)->toHaveCount(0);
});

it('can mix regular lines with html lines', function () {
    $message = new TrackableNotificationMailMessage;

    $message
        ->line('Regular text')
        ->htmlLine('<strong>Bold</strong>')
        ->blankLine()
        ->line('Another regular line');

    expect($message->introLines)->toHaveCount(4)
        ->and($message->introLines[0])->toBeString()
        ->and($message->introLines[1])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[2])->toBeInstanceOf(HtmlString::class)
        ->and($message->introLines[3])->toBeString();
});
