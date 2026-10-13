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
    Schema::create('document_links', function (Blueprint $table) {
        $table->id('link_id');
        $table->foreignId('document_id')->constrained(table: 'documents', column: 'document_id')->cascadeOnDelete();
        $table->string('link_type', 50);
        $table->string('reference_title', 255);
        $table->string('reference_number', 100)->nullable();
        $table->string('source_url', 500)->nullable();
        $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
        $table->timestamp('added_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('document_links');
}
};
