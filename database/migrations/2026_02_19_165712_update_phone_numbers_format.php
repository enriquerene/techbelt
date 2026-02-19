<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Helpers\PhoneNormalizer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update users table
        $users = DB::table('users')->whereNotNull('phone')->get();
        
        foreach ($users as $user) {
            $normalized = PhoneNormalizer::normalize($user->phone);
            if ($normalized) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['phone' => $normalized]);
            }
        }
        
        // Update invites table
        $invites = DB::table('invites')->whereNotNull('phone')->get();
        
        foreach ($invites as $invite) {
            $normalized = PhoneNormalizer::normalize($invite->phone);
            if ($normalized) {
                DB::table('invites')
                    ->where('id', $invite->id)
                    ->update(['phone' => $normalized]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * Note: This is not perfect as we can't restore the original +55 format
     * without additional data. We'll convert back by adding +55 prefix.
     */
    public function down(): void
    {
        // For users table - add +55 prefix back
        $users = DB::table('users')->whereNotNull('phone')->get();
        
        foreach ($users as $user) {
            $digits = $user->phone;
            // If it's 10 or 11 digits, add +55
            if (strlen($digits) === 10 || strlen($digits) === 11) {
                $reverted = '+55' . $digits;
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['phone' => $reverted]);
            }
        }
        
        // For invites table - add +55 prefix back
        $invites = DB::table('invites')->whereNotNull('phone')->get();
        
        foreach ($invites as $invite) {
            $digits = $invite->phone;
            // If it's 10 or 11 digits, add +55
            if (strlen($digits) === 10 || strlen($digits) === 11) {
                $reverted = '+55' . $digits;
                DB::table('invites')
                    ->where('id', $invite->id)
                    ->update(['phone' => $reverted]);
            }
        }
    }
};
