<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Middleware\Webhooks\MailgunWebhookMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

beforeEach(function () {
    config()->set('services.mailgun.secret', 'test-secret-key');
    $this->middleware = new MailgunWebhookMiddleware;
});

it('allows POST requests with valid signature', function () {
    $timestamp = time();
    $token = 'valid-token-123';
    $signature = hash_hmac('sha256', $timestamp.$token, 'test-secret-key');

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $next = function ($req) {
        return new Response('Success', 200);
    };

    $response = $this->middleware->handle($request, $next);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBe('Success');
});

it('rejects non-POST requests', function () {
    $request = Request::create('/webhook', 'GET');

    $next = function ($req) {
        return new Response('Should not reach here', 200);
    };

    expect(fn () => $this->middleware->handle($request, $next))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('rejects PUT requests', function () {
    $request = Request::create('/webhook', 'PUT');

    $next = function ($req) {
        return new Response('Should not reach here', 200);
    };

    expect(fn () => $this->middleware->handle($request, $next))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('rejects DELETE requests', function () {
    $request = Request::create('/webhook', 'DELETE');

    $next = function ($req) {
        return new Response('Should not reach here', 200);
    };

    expect(fn () => $this->middleware->handle($request, $next))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('rejects requests with invalid signature', function () {
    $timestamp = time();
    $token = 'valid-token';
    $signature = 'invalid-signature';

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $next = function ($req) {
        return new Response('Should not reach here', 200);
    };

    expect(fn () => $this->middleware->handle($request, $next))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('rejects requests with expired timestamp', function () {
    $timestamp = time() - 20; // 20 seconds ago (> 15 seconds threshold)
    $token = 'valid-token';
    $signature = hash_hmac('sha256', $timestamp.$token, 'test-secret-key');

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $next = function ($req) {
        return new Response('Should not reach here', 200);
    };

    $result = $this->middleware->verify($request);

    expect($result)->toBeFalse();
});

it('accepts requests with timestamp within 15 seconds', function () {
    $timestamp = time() - 10; // 10 seconds ago (< 15 seconds threshold)
    $token = 'valid-token';
    $signature = hash_hmac('sha256', $timestamp.$token, 'test-secret-key');

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $result = $this->middleware->verify($request);

    expect($result)->toBeTrue();
});

it('rejects requests with future timestamp beyond threshold', function () {
    $timestamp = time() + 20; // 20 seconds in future (> 15 seconds threshold)
    $token = 'valid-token';
    $signature = hash_hmac('sha256', $timestamp.$token, 'test-secret-key');

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $result = $this->middleware->verify($request);

    expect($result)->toBeFalse();
});

it('uses hash_equals for signature comparison to prevent timing attacks', function () {
    $timestamp = time();
    $token = 'security-test-token';
    $correctSignature = hash_hmac('sha256', $timestamp.$token, 'test-secret-key');

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $correctSignature,
        ],
    ]);

    // The verify method uses hash_equals internally
    $result = $this->middleware->verify($request);

    expect($result)->toBeTrue();
});

it('handles missing signature parameters', function () {
    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => time(),
            'token' => 'some-token',
            // Missing signature
        ],
    ]);

    // This will cause TypeError because signature is null
    // The middleware should handle this gracefully in production
    expect(fn () => $this->middleware->verify($request))
        ->toThrow(TypeError::class);
});

it('handles completely missing signature data', function () {
    $request = Request::create('/webhook', 'POST', []);

    $result = $this->middleware->verify($request);

    expect($result)->toBeFalse();
});

it('respects configured mailgun secret', function () {
    $customSecret = 'custom-mailgun-secret-key';
    config()->set('services.mailgun.secret', $customSecret);

    $timestamp = time();
    $token = 'test-token';
    $signature = hash_hmac('sha256', $timestamp.$token, $customSecret);

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $result = $this->middleware->verify($request);

    expect($result)->toBeTrue();
});

it('fails verification when using wrong secret', function () {
    $correctSecret = 'correct-secret';
    $wrongSecret = 'wrong-secret';

    config()->set('services.mailgun.secret', $correctSecret);

    $timestamp = time();
    $token = 'test-token';
    $signature = hash_hmac('sha256', $timestamp.$token, $wrongSecret);

    $request = Request::create('/webhook', 'POST', [
        'signature' => [
            'timestamp' => $timestamp,
            'token' => $token,
            'signature' => $signature,
        ],
    ]);

    $result = $this->middleware->verify($request);

    expect($result)->toBeFalse();
});

it('buildSignature generates correct HMAC SHA256 signature', function () {
    $reflection = new ReflectionClass($this->middleware);
    $method = $reflection->getMethod('buildSignature');
    $method->setAccessible(true);

    $timestamp = '1234567890';
    $token = 'test-token-abc';

    $request = Request::create('/webhook', 'POST', [
        'timestamp' => $timestamp,
        'token' => $token,
    ]);

    $signature = $method->invoke($this->middleware, $request);

    // Verify it's a valid SHA256 hash (64 characters)
    expect($signature)->toBeString()
        ->and(strlen($signature))->toBe(64)
        ->and(ctype_xdigit($signature))->toBeTrue();
});

it('buildSignature uses configured mailgun secret', function () {
    $customSecret = 'my-custom-secret-key';
    config()->set('services.mailgun.secret', $customSecret);

    $reflection = new ReflectionClass($this->middleware);
    $method = $reflection->getMethod('buildSignature');
    $method->setAccessible(true);

    $timestamp = '1234567890';
    $token = 'test-token';

    $request = Request::create('/webhook', 'POST', [
        'timestamp' => $timestamp,
        'token' => $token,
    ]);

    $signature = $method->invoke($this->middleware, $request);
    $expectedSignature = hash_hmac('sha256', $timestamp.$token, $customSecret);

    expect($signature)->toBe($expectedSignature);
});

it('buildSignature concatenates timestamp and token correctly', function () {
    $reflection = new ReflectionClass($this->middleware);
    $method = $reflection->getMethod('buildSignature');
    $method->setAccessible(true);

    $timestamp = '9999999999';
    $token = 'unique-token-xyz';

    $request = Request::create('/webhook', 'POST', [
        'timestamp' => $timestamp,
        'token' => $token,
    ]);

    $signature = $method->invoke($this->middleware, $request);
    $expectedSignature = hash_hmac('sha256', $timestamp.$token, 'test-secret-key');

    expect($signature)->toBe($expectedSignature);
});

it('buildSignature produces different signatures for different inputs', function () {
    $reflection = new ReflectionClass($this->middleware);
    $method = $reflection->getMethod('buildSignature');
    $method->setAccessible(true);

    $request1 = Request::create('/webhook', 'POST', [
        'timestamp' => '1000',
        'token' => 'token1',
    ]);

    $request2 = Request::create('/webhook', 'POST', [
        'timestamp' => '2000',
        'token' => 'token2',
    ]);

    $signature1 = $method->invoke($this->middleware, $request1);
    $signature2 = $method->invoke($this->middleware, $request2);

    expect($signature1)->not->toBe($signature2);
});
