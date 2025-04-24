<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {

            $table->id();

            $table->string('message_id')->index();
            $table->nullableMorphs('sender');
            $table->string('subject')->nullable();
            $table->string('to')->nullable();
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('reply_to')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->integer('opened')->default(0);
            $table->integer('clicked')->default(0);
            $table->unsignedMediumInteger('delivery_status_attempts')->nullable();
            $table->text('delivery_status_message')->nullable();

            $table->dateTime('first_opened_at')->nullable();
            $table->dateTime('first_clicked_at')->nullable();
            $table->dateTime('last_opened_at')->nullable();
            $table->dateTime('last_clicked_at')->nullable();

            $table->timestamps();
        });
    }
};
