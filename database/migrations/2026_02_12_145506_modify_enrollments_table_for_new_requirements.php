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
            if (Schema::hasColumn('enrollments', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->change();
            }
            
            // Add is_custom_price flag to track admin overrides if it doesn't exist
            if (!Schema::hasColumn('enrollments', 'is_custom_price')) {
                $table->boolean('is_custom_price')->default(false)->after('amount');
            }
            
            // Remove payment_method column (moved to payments table)
            if (Schema::hasColumn('enrollments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            
            // Remove class_id column (replaced with many-to-many relationship)
            if (Schema::hasColumn('enrollments', 'class_id')) {
                // Try to drop the foreign key if it exists
                try {
                    $table->dropForeign(['class_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist or have a different name
                    // Try to drop it by conventional name
                    try {
                        $table->dropForeign('enrollments_class_id_foreign');
                    } catch (\Exception $e2) {
                        // Ignore if foreign key doesn't exist
                    }
                }
                
                // Then drop the unique constraint
                $table->dropUnique('enrollments_user_id_class_id_unique');
                // Finally drop the column
                $table->dropColumn('class_id');
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
            if (Schema::hasColumn('enrollments', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0)->nullable(false)->change();
            }
            
            // Remove is_custom_price flag if it exists
            if (Schema::hasColumn('enrollments', 'is_custom_price')) {
                $table->dropColumn('is_custom_price');
            }
            
            // Add back payment_method column if it doesn't exist
            if (!Schema::hasColumn('enrollments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }
            
            // Add back class_id column if it doesn't exist
            if (!Schema::hasColumn('enrollments', 'class_id')) {
                $table->foreignId('class_id')->nullable()->constrained('classes')->after('user_id');
                // Restore the unique constraint
                $table->unique(['user_id', 'class_id']);
            }
        });
    }
};
