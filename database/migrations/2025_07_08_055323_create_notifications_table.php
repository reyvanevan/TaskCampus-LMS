<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'assignment_created', 'assignment_graded', 'deadline_reminder'
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data (assignment_id, course_id, etc.)
            $table->timestamp('read_at')->nullable();
            $table->timestamp('scheduled_for')->nullable(); // For deadline reminders
            $table->boolean('sent_email')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'read_at']);
            $table->index(['type', 'scheduled_for']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
