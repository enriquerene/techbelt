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
        // First, drop the unique index on email (required before making nullable in some DBs)
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
        
        // Make email nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
        
        // Make phone required (remove nullable)
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
        });
        
        // Add unique index back for email where email is not null (optional)
        // We'll add a unique index that allows multiple nulls
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['email'], 'users_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the unique index
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
        
        // Make email not nullable again
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
        
        // Make phone nullable again
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });
        
        // Add back the original unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['email']);
        });
    }
};
