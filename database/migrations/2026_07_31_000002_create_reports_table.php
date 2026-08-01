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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('tracking_code', 32)->unique();
            $table->string('tracking_hash', 64)->unique();
            $table->string('title');
            $table->text('description'); // Plain text as requested
            $table->date('incident_date')->nullable();
            $table->string('incident_location')->nullable();
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('pending'); // pending, under_review, investigating, resolved, rejected
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('internal_notes')->nullable(); // Encrypted at model layer
            $table->timestamps();

            $table->index('tracking_hash');
            $table->index('status');
            $table->index(['category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
