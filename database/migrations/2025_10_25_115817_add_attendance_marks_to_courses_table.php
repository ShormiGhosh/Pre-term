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
            $table->integer('attendance_total_marks')->default(10)->after('teacher_id');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('marks', 5, 2)->nullable()->after('marked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('attendance_total_marks');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('marks');
        });
    }
};
