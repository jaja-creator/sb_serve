<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_proposals', function (Blueprint $table) {
            $table->id('proposal_id');
            $table->string('proposal_type', 20);
            $table->string('title', 255);
            $table->text('description');
            $table->string('proposer_name', 150);
            $table->string('proposer_email', 255);
            $table->string('proposer_phone', 30)->nullable();
            $table->string('proposer_barangay', 100)->nullable();
            $table->string('status', 20)->default('Pending');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_proposals');
    }
};