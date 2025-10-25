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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'ct_marks_added', 'low_attendance', etc.
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable();
            $table->string('link')->nullable(); // URL to navigate when clicked
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->index(['student_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
