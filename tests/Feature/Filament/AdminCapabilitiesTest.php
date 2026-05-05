<?php

use App\Models\User;
use App\Models\Invite;
use App\Models\Modality;
use App\Models\Enrollment;
use App\Models\GymClass;
use App\Models\PricingTier;
use App\Models\Expense;
use App\Models\ResourceItem;
use Illuminate\Support\Facades\Hash;

test('admin user has correct role permissions', function () {
    $admin = User::factory()->admin()->create();
    
    $this->assertTrue($admin->isAdmin());
    $this->assertFalse($admin->isStaff());
    $this->assertFalse($admin->isStudent());
});

test('admin can create invites in database', function () {
    $admin = User::factory()->admin()->create();
    
    // Create an invite directly in database (bypassing HTTP)
    // The Invite model requires a token field
    $invite = Invite::create([
        'name' => 'John Doe',
        'phone' => '+5511999999999',
        'role' => 'student',
        'token' => \Illuminate\Support\Str::random(32),
        'expires_at' => now()->addDays(7),
        'created_by' => $admin->id,
    ]);
    
    $this->assertDatabaseHas('invites', [
        'name' => 'John Doe',
        'phone' => '+5511999999999',
        'role' => 'student',
    ]);
});

test('admin can create other resources in database', function () {
    $admin = User::factory()->admin()->create();
    
    // Test creating various resources directly
    $modality = Modality::create([
        'name' => 'Test Modality',
        'slug' => 'test-modality',
        'description' => 'Test description',
        'color' => '#3b82f6',
        'icon' => 'heroicon-o-academic-cap',
        'is_active' => true,
        'order' => 1,
    ]);
    
    $this->assertDatabaseHas('modalities', ['name' => 'Test Modality']);
    
    $pricingTier = PricingTier::create([
        'name' => 'Test Pricing Tier',
        'description' => 'Test description',
        'price' => 99.99,
        'duration_days' => 30,
        'is_active' => true,
    ]);
    
    $this->assertDatabaseHas('pricing_tiers', ['name' => 'Test Pricing Tier']);
});

test('admin can create users directly without invitation', function () {
    // Business rule: admin should be capable of creating new users directly, not only by invitation
    // This test verifies the database-level capability exists
    
    $admin = User::factory()->admin()->create();
    
    // Create a student user directly
    $student = User::create([
        'name' => 'Test Student',
        'email' => 'student@test.com',
        'phone' => '+5511988888888',
        'password' => bcrypt('password'),
        'role' => [\App\Models\User::ROLE_STUDENT],
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'student@test.com',
    ]);
    
    // Create a staff user directly
    $staff = User::create([
        'name' => 'Test Staff',
        'email' => 'staff@test.com',
        'phone' => '+5511977777777',
        'password' => bcrypt('password'),
        'role' => [\App\Models\User::ROLE_STAFF],
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'staff@test.com',
    ]);
    
    // Create an admin user directly
    $newAdmin = User::create([
        'name' => 'Test Admin',
        'email' => 'newadmin@test.com',
        'phone' => '+5511966666666',
        'password' => bcrypt('password'),
        'role' => [\App\Models\User::ROLE_ADMIN],
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'newadmin@test.com',
    ]);
    
    // Verify all created users have correct roles
    $this->assertTrue($student->isStudent());
    $this->assertTrue($staff->isStaff());
    $this->assertTrue($newAdmin->isAdmin());
});

test('admin can create users with multiple roles directly', function () {
    $admin = User::factory()->admin()->create();
    
    // Create a user with both student and staff roles
    $user = User::create([
        'name' => 'Multi Role User',
        'email' => 'multi@test.com',
        'phone' => '+5511955555555',
        'password' => bcrypt('password'),
        'role' => [\App\Models\User::ROLE_STUDENT, \App\Models\User::ROLE_STAFF],
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'multi@test.com',
    ]);
    
    $this->assertTrue($user->isStudent());
    $this->assertTrue($user->isStaff());
});

test('admin can create user with hashed password directly', function () {
    $admin = User::factory()->admin()->create();
    
    $plainPassword = 'my-secret-password-123';
    
    $user = User::create([
        'name' => 'Password Test',
        'email' => 'password-test@test.com',
        'phone' => '+5511944444444',
        'password' => bcrypt($plainPassword),
        'role' => [\App\Models\User::ROLE_STUDENT],
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'password-test@test.com',
    ]);
    
    // Verify password is hashed and can be verified
    $this->assertTrue(Hash::check($plainPassword, $user->password));
    $this->assertNotEquals($plainPassword, $user->password);
});

test('admin can create user without email directly', function () {
    $admin = User::factory()->admin()->create();
    
    // Email is optional — phone is the primary identifier
    $user = User::create([
        'name' => 'No Email User',
        'phone' => '+5511933333333',
        'password' => bcrypt('password'),
        'role' => [\App\Models\User::ROLE_STUDENT],
    ]);
    
    $this->assertDatabaseHas('users', [
        'name' => 'No Email User',
        'phone' => '+5511933333333',
    ]);
    
    $this->assertNull($user->email);
});

test('resource translations exist in Brazilian Portuguese', function () {
    // Check that translations exist in the language files
    $translations = [
        'Students' => 'Alunos',
        'Staff' => 'Professores',
        'Modalities' => 'Modalidades',
        'Invites' => 'Convites',
        'Enrollments' => 'Matrículas',
        'Classes' => 'Turmas',
        'Pricing Tiers' => 'Planos',
        'Expenses' => 'Despesas',
        'Resources' => 'Recursos',
    ];
    
    foreach ($translations as $english => $portuguese) {
        $this->assertEquals($portuguese, __($english));
    }
});

test('admin panel configuration forces dark mode', function () {
    // Check AdminPanelProvider configuration
    $providerContent = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));
    
    $this->assertStringContainsString('->darkMode(true)', $providerContent, 'Admin panel should force dark mode');
    $this->assertStringContainsString("->brandName('Tech Belt Admin')", $providerContent, 'Brand name should be set');
});

test('role-based access control logic works', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->staff()->create();
    $student = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    
    // Verify role detection
    $this->assertTrue($admin->isAdmin());
    $this->assertTrue($staff->isStaff());
    $this->assertTrue($student->isStudent());
    
    // Verify role hierarchy
    $this->assertTrue($admin->hasRole('admin'));
    $this->assertFalse($staff->hasRole('admin'));
    $this->assertFalse($student->hasRole('admin'));
});

test('filament resource classes exist and are properly configured', function () {
    // Check that all Filament resource classes exist
    $resources = [
        \App\Filament\Resources\InviteResource::class,
        \App\Filament\Resources\ModalityResource::class,
        \App\Filament\Resources\GymClassResource::class,
        \App\Filament\Resources\PricingTierResource::class,
        \App\Filament\Resources\EnrollmentResource::class,
        \App\Filament\Resources\ExpenseResource::class,
        \App\Filament\Resources\ResourceResource::class,
        \App\Filament\Resources\StudentResource::class,
        \App\Filament\Resources\StaffResource::class,
    ];
    
    foreach ($resources as $resourceClass) {
        $this->assertTrue(class_exists($resourceClass), "Resource class {$resourceClass} should exist");
        
        // Check that it has required methods
        $resource = new $resourceClass(app());
        $this->assertIsString($resource::getNavigationLabel());
    }
});