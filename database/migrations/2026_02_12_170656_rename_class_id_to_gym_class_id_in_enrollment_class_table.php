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
        Schema::table('enrollment_class', function (Blueprint $table) {
            // Rename class_id to gym_class_id to follow Laravel conventions
            $table->renameColumn('class_id', 'gym_class_id');
            
            // Drop the existing foreign key constraint
            $table->dropForeign(['class_id']);
            
            // Re-add foreign key with new column name
            $table->foreign('gym_class_id')->references('id')->on('classes')->cascadeOnDelete();
            
            // Update the unique constraint
            $table->dropUnique(['enrollment_id', 'class_id']);
            $table->unique(['enrollment_id', 'gym_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_class', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['gym_class_id']);
            
            // Drop unique constraint
            $table->dropUnique(['enrollment_id', 'gym_class_id']);
            
            // Rename back
            $table->renameColumn('gym_class_id', 'class_id');
            
            // Re-add foreign key
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            
            // Re-add unique constraint
            $table->unique(['enrollment_id', 'class_id']);
        });
    }
};
