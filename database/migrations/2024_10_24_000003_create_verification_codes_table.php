<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates verification_codes table for email verification and password reset
     */
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Email address to verify
            $table->string('code', 6); // 6-digit verification code
            $table->enum('type', ['signup', 'reset']); // Type: signup verification or password reset
            $table->timestamp('expires_at'); // Code expiration time
            $table->boolean('is_used')->default(false); // Track if code was used
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['email', 'code', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
