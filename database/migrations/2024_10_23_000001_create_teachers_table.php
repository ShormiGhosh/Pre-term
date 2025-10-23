<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the teachers table for storing teacher information
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Teacher's full name
            $table->string('email')->unique(); // Format: teacher_first_name@dept.kuet.ac.bd
            $table->string('password'); // Hashed password
            $table->string('department'); // e.g., CSE, EEE, ME, etc.
            $table->string('designation')->nullable(); // Professor, Associate Professor, etc.
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
