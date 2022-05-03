<?php

use AppsInteligentes\EmailTracking\Controllers\MailgunWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->group(function () {
    Route::post('mailgun', MailgunWebhookController::class)
        ->name('email-tracking.webhooks.mailgun');
});
