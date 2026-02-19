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
        Schema::table('resource_items', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['responsible_user_id']);
            
            // Drop the columns
            $table->dropColumn(['unit_cost', 'total_cost', 'responsible_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_items', function (Blueprint $table) {
            // Re-add the columns
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity');
            $table->decimal('total_cost', 10, 2)->nullable()->after('unit_cost');
            $table->foreignId('responsible_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });
    }
};
