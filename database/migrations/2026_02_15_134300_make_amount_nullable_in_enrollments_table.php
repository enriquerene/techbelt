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
            // Make amount nullable (will use pricing tier price as default)
            $table->decimal('amount', 10, 2)->nullable()->change();
            
            // Add is_custom_price flag to track admin overrides
            if (!Schema::hasColumn('enrollments', 'is_custom_price')) {
                $table->boolean('is_custom_price')->default(false)->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Restore amount as not nullable with default
            $table->decimal('amount', 10, 2)->default(0)->nullable(false)->change();
            
            // Remove is_custom_price column if it exists
            if (Schema::hasColumn('enrollments', 'is_custom_price')) {
                $table->dropColumn('is_custom_price');
            }
        });
    }
};
