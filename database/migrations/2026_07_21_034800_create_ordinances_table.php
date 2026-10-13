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
    Schema::create('ordinances', function (Blueprint $table) {
        $table->id('ordinance_id');
        $table->foreignId('document_id')->constrained(table: 'documents', column: 'document_id')->cascadeOnDelete();
        $table->string('ordinance_number', 50)->nullable();
        $table->string('stage', 20);
        $table->string('version_number', 20);
        $table->string('author', 150);
        $table->string('committee_referred', 150)->nullable();
        $table->date('date_prepared');
        $table->date('date_enacted')->nullable();
        $table->string('implementing_agency', 150)->nullable();
        $table->string('target_beneficiaries', 255)->nullable();
        $table->string('file_path', 500)->nullable();
        $table->text('remarks')->nullable();
        $table->foreignId('encoded_by')->constrained('users')->cascadeOnDelete();
        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('ordinances');
}
};
