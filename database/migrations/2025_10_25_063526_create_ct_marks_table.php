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
        Schema::create('ct_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ct_schedule_id')->constrained('ct_schedules')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2)->nullable(); // e.g., 18.75 out of 20
            $table->timestamps();
            
            // Ensure one mark entry per student per CT
            $table->unique(['ct_schedule_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_marks');
    }
};
