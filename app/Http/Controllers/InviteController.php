<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function show($token)
    {
        $invite = Invite::where('token', $token)
            ->whereNull('used_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        return view('invite.accept', compact('invite'));
    }

    public function accept(Request $request, $token)
    {
        $invite = Invite::where('token', $token)
            ->whereNull('used_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $invite->name ?? $invite->phone, // Use invite name if available, otherwise phone
            'phone' => $invite->phone,
            'email' => null, // Email is optional
            'password' => Hash::make($request->password),
            'role' => [$invite->role], // Convert string role to array
            'email_verified_at' => now(),
        ]);

        $invite->update(['used_at' => now()]);

        auth()->login($user);

        // Redirect based on subscription status
        if ($user->isStudent() && !$user->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('onboarding');
        }

        return redirect()->route('app.home');
    }
}