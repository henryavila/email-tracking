<?php

use HenryAvila\EmailTracking\Controllers\MailgunWebhookController;
use HenryAvila\EmailTracking\Middleware\Webhooks\MailgunWebhookMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([MailgunWebhookController::class])->prefix('webhooks')->group(function () {
    Route::post('mailgun', MailgunWebhookController::class)
         ->middleware(MailgunWebhookMiddleware::class)
         ->name('email-tracking.webhooks.mailgun');
});
