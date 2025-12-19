<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Listeners\LogEmailSentListener;
use HenryAvila\EmailTracking\Mail\TrackableMail;
use HenryAvila\EmailTracking\Models\Email;
use HenryAvila\EmailTracking\Models\User;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

/**
 * Test Enum for email types
 */
enum TestEmailType: string
{
    case TRANSACTIONAL = 'transactional';
    case MARKETING = 'marketing';
    case NOTIFICATION = 'notification';
}

/**
 * Test Mailable that returns a BackedEnum type
 */
class MailableWithEnumType extends TrackableMail
{
    public function __construct($model)
    {
        parent::__construct($model, 'emails.sample');
    }

    protected function getEmailType(): TestEmailType
    {
        return TestEmailType::TRANSACTIONAL;
    }
}

/**
 * Test Mailable that returns a string type
 */
class MailableWithStringType extends TrackableMail
{
    public function __construct($model)
    {
        parent::__construct($model, 'emails.sample');
    }

    protected function getEmailType(): string
    {
        return 'custom_type';
    }
}

/**
 * Test Mailable WITHOUT getEmailType method (backward compatibility)
 */
class MailableWithoutType extends TrackableMail
{
    public function __construct($model)
    {
        parent::__construct($model, 'emails.sample');
    }

    // No getEmailType() method - should work fine
}

// No beforeEach needed - migrations are loaded automatically via TestCase

it('captures email type from BackedEnum in buildViewData', function () {
    $user = User::factory()->create();
    $mailable = (new MailableWithEnumType($user))->to($user->email)->from($user->email);

    $viewData = $mailable->buildViewData();

    expect($viewData)->toHaveKey('__email_type')
        ->and($viewData['__email_type'])->toBe('transactional')
        ->and($viewData['__email_type'])->toBeString();
});

it('captures email type from string in buildViewData', function () {
    $user = User::factory()->create();
    $mailable = (new MailableWithStringType($user))->to($user->email)->from($user->email);

    $viewData = $mailable->buildViewData();

    expect($viewData)->toHaveKey('__email_type')
        ->and($viewData['__email_type'])->toBe('custom_type')
        ->and($viewData['__email_type'])->toBeString();
});

it('does not add email_type when getEmailType method is missing', function () {
    $user = User::factory()->create();
    $mailable = (new MailableWithoutType($user))->to($user->email)->from($user->email);

    $viewData = $mailable->buildViewData();

    expect($viewData)->not->toHaveKey('__email_type');
});

it('stores email_type with BackedEnum in database via LogEmailSentListener', function () {
    $user = User::factory()->create();

    Event::fake([MessageSent::class]);

    assertDatabaseCount((new Email)->getTable(), 0);

    $mailable = (new MailableWithEnumType($user))->to($user->email)->from($user->email);
    Mail::send($mailable);

    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        $listener = new LogEmailSentListener;
        $listener->handle($event);

        assertDatabaseCount((new Email)->getTable(), 1);

        $email = Email::first();

        expect($email->to)->toBe($user->email)
            ->and($email->sender_type)->toBe(User::class)
            ->and($email->sender_id)->toBe($user->id)
            ->and($email->email_type)->toBe('transactional')
            ->and($email->email_type)->toBeString();

        assertDatabaseHas((new Email)->getTable(), [
            'id' => 1,
            'email_type' => 'transactional',
        ]);

        return true;
    });
});

it('stores email_type with string in database via LogEmailSentListener', function () {
    $user = User::factory()->create();

    Event::fake([MessageSent::class]);

    assertDatabaseCount((new Email)->getTable(), 0);

    $mailable = (new MailableWithStringType($user))->to($user->email)->from($user->email);
    Mail::send($mailable);

    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        $listener = new LogEmailSentListener;
        $listener->handle($event);

        assertDatabaseCount((new Email)->getTable(), 1);

        $email = Email::first();

        expect($email->to)->toBe($user->email)
            ->and($email->email_type)->toBe('custom_type');

        assertDatabaseHas((new Email)->getTable(), [
            'id' => 1,
            'email_type' => 'custom_type',
        ]);

        return true;
    });
});

it('stores null email_type when getEmailType is missing (backward compatibility)', function () {
    $user = User::factory()->create();

    Event::fake([MessageSent::class]);

    assertDatabaseCount((new Email)->getTable(), 0);

    $mailable = (new MailableWithoutType($user))->to($user->email)->from($user->email);
    Mail::send($mailable);

    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user) {
        $listener = new LogEmailSentListener;
        $listener->handle($event);

        assertDatabaseCount((new Email)->getTable(), 1);

        $email = Email::first();

        expect($email->to)->toBe($user->email)
            ->and($email->email_type)->toBeNull();

        assertDatabaseHas((new Email)->getTable(), [
            'id' => 1,
            'email_type' => null,
        ]);

        return true;
    });
});

it('can query emails by type', function () {
    $user = User::factory()->create();
    $listener = new LogEmailSentListener;

    // Create emails directly in database with different types
    Email::create([
        'message_id' => 'msg-1',
        'sender_type' => User::class,
        'sender_id' => $user->id,
        'subject' => 'Test 1',
        'to' => $user->email,
        'email_type' => 'transactional',
    ]);

    Email::create([
        'message_id' => 'msg-2',
        'sender_type' => User::class,
        'sender_id' => $user->id,
        'subject' => 'Test 2',
        'to' => $user->email,
        'email_type' => 'transactional',
    ]);

    Email::create([
        'message_id' => 'msg-3',
        'sender_type' => User::class,
        'sender_id' => $user->id,
        'subject' => 'Test 3',
        'to' => $user->email,
        'email_type' => 'custom_type',
    ]);

    Email::create([
        'message_id' => 'msg-4',
        'sender_type' => User::class,
        'sender_id' => $user->id,
        'subject' => 'Test 4',
        'to' => $user->email,
        'email_type' => null,
    ]);

    assertDatabaseCount((new Email)->getTable(), 4);

    // Query by type
    $transactional = Email::where('email_type', 'transactional')->get();
    expect($transactional)->toHaveCount(2);

    $customType = Email::where('email_type', 'custom_type')->get();
    expect($customType)->toHaveCount(1);

    $withoutType = Email::whereNull('email_type')->get();
    expect($withoutType)->toHaveCount(1);
});

it('maintains model data in view data when email type is present', function () {
    $user = User::factory()->create();
    $mailable = (new MailableWithEnumType($user))->to($user->email)->from($user->email);

    $viewData = $mailable->buildViewData();

    // Should have both model and email_type
    expect($viewData)->toHaveKey('model')
        ->and($viewData)->toHaveKey('__email_type')
        ->and($viewData['model'])->toBe($user)
        ->and($viewData['__email_type'])->toBe('transactional');
});
