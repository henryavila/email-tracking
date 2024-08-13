<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Controllers\MailgunWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware([MailgunWebhookController::class])->prefix('webhooks')->group(function () {
    Route::post('mailgun', MailgunWebhookController::class)
        ->name('email-tracking.webhooks.mailgun');
});
