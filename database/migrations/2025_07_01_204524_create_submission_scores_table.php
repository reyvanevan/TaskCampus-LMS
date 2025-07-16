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
        Schema::create('submission_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('rubric_criteria_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2);
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // Ensure each criteria is only scored once per submission
            $table->unique(['submission_id', 'rubric_criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_scores');
    }
};