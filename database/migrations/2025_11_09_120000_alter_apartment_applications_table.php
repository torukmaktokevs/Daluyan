<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_applications', function (Blueprint $table) {
            // Remove guests column
            if (Schema::hasColumn('apartment_applications', 'guests')) {
                $table->dropColumn('guests');
            }
        });

        Schema::table('apartment_applications', function (Blueprint $table) {
            // Rename date columns
            if (Schema::hasColumn('apartment_applications', 'checkin_date')) {
                $table->renameColumn('checkin_date', 'visit_date');
            }
            if (Schema::hasColumn('apartment_applications', 'checkout_date')) {
                $table->renameColumn('checkout_date', 'movein_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('apartment_applications', function (Blueprint $table) {
            // Add guests column back
            if (!Schema::hasColumn('apartment_applications', 'guests')) {
                $table->integer('guests')->nullable();
            }
        });

        Schema::table('apartment_applications', function (Blueprint $table) {
            // Rename columns back
            if (Schema::hasColumn('apartment_applications', 'visit_date')) {
                $table->renameColumn('visit_date', 'checkin_date');
            }
            if (Schema::hasColumn('apartment_applications', 'movein_date')) {
                $table->renameColumn('movein_date', 'checkout_date');
            }
        });
    }
};
