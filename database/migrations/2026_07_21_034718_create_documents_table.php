<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('documents', function (Blueprint $table) {
        $table->id('document_id');
        $table->string('series_number', 50);
        $table->string('title', 255);
        $table->string('document_type', 20);
        $table->string('subject_matter', 255)->nullable();
        $table->string('sponsor', 150);
        $table->string('status', 30);
        $table->date('date_filed');
        $table->date('date_approved_mayor')->nullable();
        $table->string('file_path', 500)->nullable();
        $table->foreignId('encoded_by')->constrained('users')->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('documents');
}
};
