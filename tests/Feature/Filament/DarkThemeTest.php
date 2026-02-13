<?php

use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;

test('admin panel forces dark mode in configuration', function () {
    // Check AdminPanelProvider configuration
    $providerContent = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));
    
    $this->assertStringContainsString('->darkMode(true)', $providerContent, 'Admin panel should force dark mode');
});

test('admin panel has correct color scheme configuration', function () {
    // Read the AdminPanelProvider to verify color configuration
    $providerContent = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));
    
    // Check for color configuration (should be appropriate for dark mode)
    $this->assertStringContainsString('->colors([', $providerContent, 'Admin panel should have color configuration');
    
    // Check for dark mode friendly colors
    // Primary color is Amber which works well in dark mode
    $this->assertStringContainsString("'primary' => Color::Amber", $providerContent, 'Primary color should be Amber for dark mode');
});

test('admin panel has complete configuration matching requirements', function () {
    // Verify the theme matches the project's visual requirements
    $providerContent = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));
    
    // Check for dark mode forcing
    $this->assertStringContainsString('->darkMode(true)', $providerContent, 'Admin panel must force dark mode');
    
    // Check for brand configuration
    $this->assertStringContainsString("->brandName('Scotelaro Admin')", $providerContent, 'Brand name should be set');
    $this->assertStringContainsString("->brandLogo(asset('logo.png'))", $providerContent, 'Brand logo should be set');
    
    // Check for SPA mode (single page application)
    $this->assertStringContainsString('->spa()', $providerContent, 'Admin panel should be SPA for better UX');
    
    // Check for authentication guard
    $this->assertStringContainsString("->authGuard('web')", $providerContent, 'Should use web guard');
});

test('staff panel does not force dark mode (if exists)', function () {
    // Check if StaffPanelProvider exists
    $staffProviderPath = app_path('Providers/Filament/StaffPanelProvider.php');
    
    if (file_exists($staffProviderPath)) {
        $staffProviderContent = file_get_contents($staffProviderPath);
        
        // Staff panel might not force dark mode
        // Check if it has darkMode configuration
        if (str_contains($staffProviderContent, 'darkMode')) {
            // If it has darkMode config, it might not be forced
            $this->assertStringNotContainsString('->darkMode(true)', $staffProviderContent, 'Staff panel should not force dark mode');
        }
    } else {
        $this->markTestSkipped('StaffPanelProvider does not exist');
    }
});

test('theme configuration is consistent with Laravel Filament best practices', function () {
    $providerContent = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));
    
    // Check for common Filament panel configuration
    $this->assertStringContainsString('->login()', $providerContent, 'Should have login page');
    $this->assertStringContainsString('->sidebarCollapsibleOnDesktop()', $providerContent, 'Should have collapsible sidebar');
    $this->assertStringContainsString('->globalSearchKeyBindings', $providerContent, 'Should have global search key bindings');
});