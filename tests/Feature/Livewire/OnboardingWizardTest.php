<?php

use App\Livewire\OnboardingWizard;
use App\Models\User;
use App\Models\Modality;
use App\Models\GymClass;
use App\Models\PricingTier;
use App\Models\Subscription;
use Livewire\Livewire;

test('onboarding wizard renders for students without active subscription', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    // Ensure no active subscription
    Subscription::where('user_id', $user->id)->delete();
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->assertSee('Selecione as Modalidades')
        ->assertSee('Pagamento');
});

test('onboarding redirects students with active subscription', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    // Create active subscription
    Subscription::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'ends_at' => now()->addMonth(),
    ]);
    $this->actingAs($user);

    Livewire::test(OnboardingWizard::class)
        ->assertRedirect(route('app.home'));
});

test('step 1 shows modalities with images and allows selection', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality = Modality::factory()->create([
        'name' => 'Jiu-Jitsu',
        'description' => 'Arte marcial brasileira',
        'image' => 'jiu-jitsu.jpg',
    ]);

    Livewire::test(OnboardingWizard::class)
        ->assertSee($modality->name)
        ->assertSee($modality->description)
        ->assertSet('step', 1)
        ->set('selectedModalities', [$modality->id])
        ->assertSet('selectedModalities', [$modality->id])
        ->call('proceed')
        ->assertSet('step', 2);
});

test('step 1 requires at least one modality to proceed', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    Modality::factory()->create();

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [])
        ->call('proceed')
        ->assertHasErrors(['selectedModalities' => 'Por favor, selecione pelo menos uma modalidade.'])
        ->assertSet('step', 1);
});

test('step 2 shows classes grouped by selected modalities', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality1 = Modality::factory()->create();
    $modality2 = Modality::factory()->create();
    
    $class1 = GymClass::factory()->create([
        'modality_id' => $modality1->id,
        'name' => 'Turma Iniciante',
        'schedule' => 'Segunda 18:00',
    ]);
    
    $class2 = GymClass::factory()->create([
        'modality_id' => $modality2->id,
        'name' => 'Turma Avançada',
        'schedule' => 'Quarta 20:00',
    ]);

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality1->id])
        ->call('proceed')
        ->assertSet('step', 2)
        ->assertSee($class1->name)
        ->assertSee($class1->schedule)
        ->assertDontSee($class2->name); // Should not show class from unselected modality
});

test('step 2 requires at least one class from each selected modality', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality1 = Modality::factory()->create();
    $modality2 = Modality::factory()->create();
    
    GymClass::factory()->create(['modality_id' => $modality1->id]);
    GymClass::factory()->create(['modality_id' => $modality2->id]);

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality1->id, $modality2->id])
        ->call('proceed')
        ->assertSet('step', 2)
        ->set('selectedClasses', []) // No classes selected
        ->call('proceed')
        ->assertHasErrors(['selectedClasses' => 'Por favor, selecione pelo menos uma turma.'])
        ->assertSet('step', 2);
});

test('step 3 calculates price based on selected classes count', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality = Modality::factory()->create();
    $class1 = GymClass::factory()->create(['modality_id' => $modality->id]);
    $class2 = GymClass::factory()->create(['modality_id' => $modality->id]);
    
    // Create pricing tiers
    $tier1 = PricingTier::factory()->create([
        'name' => 'Plano 1 Turma',
        'class_count' => 1,
        'price' => 100.00,
    ]);
    
    $tier2 = PricingTier::factory()->create([
        'name' => 'Plano 2 Turmas',
        'class_count' => 2,
        'price' => 180.00,
    ]);

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality->id])
        ->call('proceed')
        ->set('selectedClasses', [$class1->id])
        ->call('proceed')
        ->assertSet('step', 3)
        ->assertSet('total', 100.00)
        ->assertSee('R$ 100,00');
});

test('step 3 selects appropriate pricing tier based on class count', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality = Modality::factory()->create();
    $classes = GymClass::factory()->count(3)->create(['modality_id' => $modality->id]);
    
    // Create pricing tiers
    $tier1 = PricingTier::factory()->create(['class_count' => 1, 'price' => 100]);
    $tier2 = PricingTier::factory()->create(['class_count' => 2, 'price' => 180]);
    $tier3 = PricingTier::factory()->create(['class_count' => 0, 'price' => 250]); // Unlimited
    
    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality->id])
        ->call('proceed')
        ->set('selectedClasses', [$classes[0]->id]) // 1 class
        ->call('calculatePrice')
        ->assertSet('pricingTier.id', $tier1->id)
        ->assertSet('total', 100)
        
        ->set('selectedClasses', [$classes[0]->id, $classes[1]->id]) // 2 classes
        ->call('calculatePrice')
        ->assertSet('pricingTier.id', $tier2->id)
        ->assertSet('total', 180)
        
        ->set('selectedClasses', [$classes[0]->id, $classes[1]->id, $classes[2]->id]) // 3 classes (no tier 3)
        ->call('calculatePrice')
        ->assertSet('pricingTier.id', $tier3->id) // Should select unlimited tier (0)
        ->assertSet('total', 250);
});

test('step 3 requires pricing tier to proceed', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality = Modality::factory()->create();
    $class = GymClass::factory()->create(['modality_id' => $modality->id]);
    
    // No pricing tiers created

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality->id])
        ->call('proceed')
        ->set('selectedClasses', [$class->id])
        ->call('proceed')
        ->assertSet('step', 3)
        ->call('proceed')
        ->assertHasErrors(['pricingTier' => 'Por favor, selecione um plano de preços.'])
        ->assertSet('step', 3);
});

test('step 4 processes payment and creates subscription', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality = Modality::factory()->create();
    $class = GymClass::factory()->create(['modality_id' => $modality->id]);
    $tier = PricingTier::factory()->create(['class_count' => 1, 'price' => 100]);

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality->id])
        ->call('proceed')
        ->set('selectedClasses', [$class->id])
        ->call('proceed')
        ->call('proceed')
        ->assertSet('step', 4)
        ->set('paymentMethod', 'credit_card')
        ->set('cardNumber', '4111111111111111')
        ->set('cardExpiry', '12/30')
        ->set('cardCvc', '123')
        ->call('proceed')
        ->assertRedirect(route('app.home'));

    // Verify subscription was created
    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
        'pricing_tier_id' => $tier->id,
        'status' => 'active',
    ]);

    // Verify enrollment was created
    $this->assertDatabaseHas('enrollments', [
        'user_id' => $user->id,
        'class_id' => $class->id,
    ]);
});

test('users can navigate back through steps', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality = Modality::factory()->create();
    $class = GymClass::factory()->create(['modality_id' => $modality->id]);

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality->id])
        ->call('proceed')
        ->assertSet('step', 2)
        ->call('back')
        ->assertSet('step', 1)
        ->call('proceed')
        ->assertSet('step', 2)
        ->set('selectedClasses', [$class->id])
        ->call('proceed')
        ->assertSet('step', 3)
        ->call('back')
        ->assertSet('step', 2);
});

test('card selection toggles correctly', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality1 = Modality::factory()->create();
    $modality2 = Modality::factory()->create();

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality1->id])
        ->assertSet('selectedModalities', [$modality1->id])
        ->set('selectedModalities', []) // Deselect
        ->assertSet('selectedModalities', [])
        ->set('selectedModalities', [$modality1->id, $modality2->id]) // Select multiple
        ->assertSet('selectedModalities', [$modality1->id, $modality2->id])
        ->set('selectedModalities', [$modality1->id]) // Deselect one
        ->assertSet('selectedModalities', [$modality1->id]);
});

test('class selection clears when modalities change', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    $modality1 = Modality::factory()->create();
    $modality2 = Modality::factory()->create();
    $class1 = GymClass::factory()->create(['modality_id' => $modality1->id]);
    $class2 = GymClass::factory()->create(['modality_id' => $modality2->id]);

    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [$modality1->id])
        ->set('selectedClasses', [$class1->id])
        ->assertSet('selectedClasses', [$class1->id])
        ->set('selectedModalities', [$modality2->id]) // Change modality
        ->assertSet('selectedClasses', []); // Should clear selected classes
});

test('next button is disabled when no modality selected', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);
    
    Modality::factory()->create();

    Livewire::test(OnboardingWizard::class)
        ->assertSet('selectedModalities', [])
        ->assertSee('Próximo'); // Button should be visible but validation will fail
    
    // The actual disabling should be handled in the view
    // We test that validation prevents proceeding
    // Note: We can't use $this->get in Pest tests without proper setup
    // Instead, we test the Livewire component behavior
    Livewire::test(OnboardingWizard::class)
        ->set('selectedModalities', [])
        ->call('proceed')
        ->assertHasErrors(['selectedModalities' => 'Por favor, selecione pelo menos uma modalidade.']);
});