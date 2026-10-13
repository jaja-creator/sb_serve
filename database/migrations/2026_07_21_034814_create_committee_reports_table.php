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
    Schema::create('committee_reports', function (Blueprint $table) {
        $table->id('report_id');
        $table->foreignId('document_id')->constrained(table: 'documents', column: 'document_id')->cascadeOnDelete();
        $table->string('committee_name', 150);
        $table->string('report_title', 255);
        $table->date('report_date');
        $table->string('recommendation', 30);
        $table->text('findings')->nullable();
        $table->string('file_path', 500)->nullable();
        $table->foreignId('prepared_by')->constrained('users')->cascadeOnDelete();
        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('committee_reports');
}
};
