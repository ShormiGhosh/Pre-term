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
        Schema::table('courses', function (Blueprint $table) {
            // Drop the unique constraint on course_code
            $table->dropUnique(['course_code']);
            
            // Add composite unique constraint on course_code and teacher_id
            // This allows different teachers to have the same course code
            $table->unique(['course_code', 'teacher_id'], 'courses_code_teacher_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('courses_code_teacher_unique');
            
            // Restore the unique constraint on course_code only
            $table->unique('course_code');
        });
    }
};
