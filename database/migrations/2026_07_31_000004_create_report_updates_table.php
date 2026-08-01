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
        Schema::create('report_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('author_type'); // admin, reporter
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('message');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['report_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_updates');
    }
};
