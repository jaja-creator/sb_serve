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
    Schema::create('amendments', function (Blueprint $table) {
        $table->id('amendment_id');
        $table->foreignId('document_id')->constrained(table: 'documents', column: 'document_id')->cascadeOnDelete();
        $table->string('section_amended', 100);
        $table->text('amendment_description');
        $table->date('amendment_date');
        $table->string('amendment_status', 20);
        $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('amendments');
}
};
