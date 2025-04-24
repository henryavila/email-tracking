<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->text('body_html')->nullable();
            $table->text('body_txt')->nullable();
        });
    }

    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('body_html');
            $table->dropColumn('body_txt');
        });
    }
};
