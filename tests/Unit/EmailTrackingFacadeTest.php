<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Facades\EmailTracking as EmailTrackingFacade;
use Illuminate\Support\Facades\Facade;

it('returns correct facade accessor', function () {
    $reflection = new ReflectionClass(EmailTrackingFacade::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);

    $accessor = $method->invoke(null);

    expect($accessor)->toBe('email-tracking');
});

it('extends Laravel Facade class', function () {
    $reflection = new ReflectionClass(EmailTrackingFacade::class);

    expect($reflection->getParentClass()->getName())->toBe(Facade::class);
});

it('has correct namespace', function () {
    expect(EmailTrackingFacade::class)->toBe('HenryAvila\EmailTracking\Facades\EmailTracking');
});
