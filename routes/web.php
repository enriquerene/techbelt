<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        // Admin users go to admin panel (dark theme)
        return redirect('/admin');
    }
    
    if ($user->isStaff()) {
        // Staff users go to staff panel (light theme)
        return redirect('/staff');
    }
    
    // For students, always redirect to onboarding
    // The onboarding wizard will check for existing subscription and redirect to app if needed
    if ($user->isStudent()) {
        return redirect()->route('onboarding');
    }
    
    // Default fallback (should not reach here for authenticated users)
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Student App Routes (/app)
Route::prefix('app')->middleware(['auth', 'verified', 'check.subscription'])->group(function () {
    Route::get('/', \App\Livewire\App\Dashboard::class)->name('app.home');
    Route::get('classes', \App\Livewire\App\Classes::class)->name('app.classes');
    Route::get('enrollments', \App\Livewire\App\Enrollments::class)->name('app.enrollments');
    Route::get('profile', \App\Livewire\App\Profile::class)->name('app.profile');
    Route::get('progress', \App\Livewire\App\Progress::class)->name('app.progress');
});

// Invite routes
Route::get('invite/{token}', [\App\Http\Controllers\InviteController::class, 'show'])->name('invite.show');
Route::post('invite/{token}', [\App\Http\Controllers\InviteController::class, 'accept'])->name('invite.accept');

// Onboarding wizard
Route::get('onboarding', \App\Livewire\OnboardingWizard::class)
    ->middleware(['auth', 'verified'])
    ->name('onboarding');

require __DIR__.'/settings.php';
