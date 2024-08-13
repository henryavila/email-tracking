<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Controllers\MailgunWebhookController;
use HenryAvila\EmailTracking\Middleware\Webhooks\MailgunWebhookMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([MailgunWebhookMiddleware::class])->prefix('webhooks')->group(function () {
    Route::post('mailgun', MailgunWebhookController::class)
        ->name('email-tracking.webhooks.mailgun');
});
