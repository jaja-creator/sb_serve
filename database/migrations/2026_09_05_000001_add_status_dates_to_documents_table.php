<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->date('date_referred_committee')->nullable()->after('date_calendared');
            $table->date('date_second_reading')->nullable()->after('date_referred_committee');
            $table->date('date_third_reading')->nullable()->after('date_second_reading');
            $table->date('date_approved')->nullable()->after('date_third_reading');
            $table->date('date_vetoed')->nullable()->after('date_enacted');
            $table->date('date_returned')->nullable()->after('date_vetoed');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'date_referred_committee', 'date_second_reading', 'date_third_reading',
                'date_approved', 'date_vetoed', 'date_returned',
            ]);
        });
    }
};