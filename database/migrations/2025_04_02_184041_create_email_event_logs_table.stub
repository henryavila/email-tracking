<?php

declare(strict_types=1);

use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('email-tracking.email-event-logs-table'), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Email::class)->constrained('emails');
            $table->string('event_code')->index();
            $table->string('event_class')->index();
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('email-tracking.email-event-logs-table'));
    }
};
