<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add a temporary column to store JSON
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('role');
        });

        // Migrate existing role values to roles array
        DB::table('users')->update([
            'roles' => DB::raw('JSON_ARRAY(role)'),
        ]);

        // Drop the old role column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Rename roles to role (optional, but keep consistency)
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('roles', 'role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: add back old role column
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('roles');
        });

        // Migrate back: take first element of JSON array
        DB::table('users')->update([
            'role' => DB::raw('JSON_UNQUOTE(JSON_EXTRACT(roles, "$[0]"))'),
        ]);

        // Drop the roles column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
