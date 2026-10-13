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
    Schema::create('status_history', function (Blueprint $table) {
        $table->id('history_id');
        $table->foreignId('document_id')->constrained(table: 'documents', column: 'document_id')->cascadeOnDelete();
        $table->string('old_status', 30)->nullable();
        $table->string('new_status', 30);
        $table->text('remarks')->nullable();
        $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
        $table->timestamp('changed_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('status_history');
}
};
