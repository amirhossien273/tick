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
        Schema::create('ticket_number_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedBigInteger('last_number')->default(0);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number', 30)->unique();
            $table->foreignUuid('source_system_id')->constrained('systems');
            $table->string('external_user_id', 100);
            $table->string('external_ticket_id', 100)->nullable();
            $table->string('subject');
            $table->longText('description')->nullable();
            $table->foreignUuid('status_id')->constrained('ticket_statuses');
            $table->foreignUuid('priority_id')->constrained('ticket_priorities');
            $table->foreignUuid('category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_number_sequences');
    }
};
