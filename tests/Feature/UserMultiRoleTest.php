<?php

use App\Models\User;

test('user can have multiple roles over time', function () {
    $user = User::factory()->create(['role' => [User::ROLE_STUDENT]]);
    
    expect($user->isStudent())->toBeTrue();
    expect($user->isStaff())->toBeFalse();
    expect($user->isAdmin())->toBeFalse();
    
    // Change role to staff
    $user->role = [User::ROLE_STAFF];
    $user->save();
    
    expect($user->isStudent())->toBeFalse();
    expect($user->isStaff())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
    
    // Change role to admin
    $user->role = [User::ROLE_ADMIN];
    $user->save();
    
    expect($user->isStudent())->toBeFalse();
    expect($user->isStaff())->toBeFalse();
    expect($user->isAdmin())->toBeTrue();
});

test('user can be both student and staff if role field supports multiple values', function () {
    // Now that role is a JSON array, users can have multiple roles
    $user = User::factory()->create(['role' => [User::ROLE_STUDENT, User::ROLE_STAFF]]);
    
    expect($user->isStudent())->toBeTrue();
    expect($user->isStaff())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
    
    // Add admin role
    $user->role = [User::ROLE_STUDENT, User::ROLE_STAFF, User::ROLE_ADMIN];
    $user->save();
    
    expect($user->isStudent())->toBeTrue();
    expect($user->isStaff())->toBeTrue();
    expect($user->isAdmin())->toBeTrue();
    
    // Remove student role
    $user->role = [User::ROLE_STAFF, User::ROLE_ADMIN];
    $user->save();
    
    expect($user->isStudent())->toBeFalse();
    expect($user->isStaff())->toBeTrue();
    expect($user->isAdmin())->toBeTrue();
});

test('role constants are defined', function () {
    expect(User::ROLE_STUDENT)->toBe('student');
    expect(User::ROLE_STAFF)->toBe('staff');
    expect(User::ROLE_ADMIN)->toBe('admin');
});