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
        Schema::table('enrollments', function (Blueprint $table) {
            // Make class_id nullable to allow enrollments without a specific class
            // (since we're using many-to-many relationship through enrollment_class table)
            $table->foreignId('class_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Restore class_id as not nullable
            $table->foreignId('class_id')->nullable(false)->change();
        });
    }
};
