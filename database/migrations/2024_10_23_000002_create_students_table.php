<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the students table for storing student information
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Student's full name
            $table->string('email')->unique(); // Format: surnameRoll@stud.kuet.ac.bd
            $table->string('password'); // Hashed password
            $table->string('roll_number')->unique(); // Student roll number
            $table->string('department'); // e.g., CSE, EEE, ME, etc.
            $table->integer('year'); // 1st, 2nd, 3rd, 4th year
            $table->string('semester'); // e.g., 1st, 2nd
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
