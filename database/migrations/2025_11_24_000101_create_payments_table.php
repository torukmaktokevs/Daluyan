<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_user_id')->nullable();
            $table->unsignedBigInteger('apartment_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('method')->default('cash');
            $table->string('status')->default('completed');
            $table->timestamps();

            $table->index('tenant_user_id');
            $table->index('apartment_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
