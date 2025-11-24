<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_messages', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('message');
        });
    }

    public function down()
    {
        Schema::table('application_messages', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
