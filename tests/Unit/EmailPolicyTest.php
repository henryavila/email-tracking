<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Models\Email;
use HenryAvila\EmailTracking\Models\User;
use HenryAvila\EmailTracking\Policies\EmailPolicy;

beforeEach(function () {
    $this->policy = new EmailPolicy;
    $this->user = User::factory()->create();
    $this->email = Email::create([
        'message_id' => 'test-message-id',
        'subject' => 'Test Subject',
        'to' => 'test@example.com',
    ]);
});

it('allows viewing any emails', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows viewing any emails without user parameter', function () {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows viewing a specific email', function () {
    expect($this->policy->view($this->user, $this->email))->toBeTrue();
});

it('allows viewing a specific email without parameters', function () {
    expect($this->policy->view())->toBeTrue();
});

it('denies creating emails', function () {
    expect($this->policy->create($this->user))->toBeFalse();
});

it('denies creating emails without user parameter', function () {
    expect($this->policy->create())->toBeFalse();
});

it('denies updating emails', function () {
    expect($this->policy->update($this->user, $this->email))->toBeFalse();
});

it('denies updating emails without parameters', function () {
    expect($this->policy->update())->toBeFalse();
});

it('denies deleting emails', function () {
    expect($this->policy->delete($this->user, $this->email))->toBeFalse();
});

it('denies deleting emails without parameters', function () {
    expect($this->policy->delete())->toBeFalse();
});

it('denies restoring emails', function () {
    expect($this->policy->restore($this->user, $this->email))->toBeFalse();
});

it('denies restoring emails without parameters', function () {
    expect($this->policy->restore())->toBeFalse();
});

it('denies force deleting emails', function () {
    expect($this->policy->forceDelete($this->user, $this->email))->toBeFalse();
});

it('denies force deleting emails without parameters', function () {
    expect($this->policy->forceDelete())->toBeFalse();
});

it('is a read-only policy', function () {
    // Can view
    expect($this->policy->viewAny())->toBeTrue()
        ->and($this->policy->view())->toBeTrue();

    // Cannot modify
    expect($this->policy->create())->toBeFalse()
        ->and($this->policy->update())->toBeFalse()
        ->and($this->policy->delete())->toBeFalse()
        ->and($this->policy->restore())->toBeFalse()
        ->and($this->policy->forceDelete())->toBeFalse();
});

it('maintains read-only behavior across different users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $email = Email::create([
        'message_id' => 'multi-user-test',
        'subject' => 'Test',
        'to' => 'test@example.com',
    ]);

    // Both users can view
    expect($this->policy->viewAny($user1))->toBeTrue()
        ->and($this->policy->viewAny($user2))->toBeTrue()
        ->and($this->policy->view($user1, $email))->toBeTrue()
        ->and($this->policy->view($user2, $email))->toBeTrue();

    // Neither can modify
    expect($this->policy->create($user1))->toBeFalse()
        ->and($this->policy->create($user2))->toBeFalse()
        ->and($this->policy->update($user1, $email))->toBeFalse()
        ->and($this->policy->update($user2, $email))->toBeFalse()
        ->and($this->policy->delete($user1, $email))->toBeFalse()
        ->and($this->policy->delete($user2, $email))->toBeFalse();
});

it('uses HandlesAuthorization trait', function () {
    $traits = class_uses(EmailPolicy::class);

    expect($traits)->toHaveKey('Illuminate\Auth\Access\HandlesAuthorization');
});
