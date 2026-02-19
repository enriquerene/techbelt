<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, create default modalities if none exist
        if (\App\Models\Modality::count() === 0) {
            $factory = new \Database\Factories\ModalityFactory();
            $defaultModalities = $factory->defaultModalities();
            
            foreach ($defaultModalities as $modalityData) {
                \App\Models\Modality::create($modalityData);
            }
            
            $this->command->info("Created " . count($defaultModalities) . " default modalities");
        }

        // Create a pricing tier if none exists
        $pricingTier = \App\Models\PricingTier::first();
        if (!$pricingTier) {
            $pricingTier = \App\Models\PricingTier::create([
                'name' => 'Basic Plan',
                'notes' => 'Access to 3 classes per week',
                'price' => 99.90,
                'class_count' => 3,
            ]);
            $this->command->info("Created pricing tier: {$pricingTier->name}");
        }

        // Clean up any existing users
        User::query()->delete();
        $this->command->info("Cleaned up existing users");

        // 1. Rafael Silva - Admin & Instructor
        $rafael = User::factory()->create([
            'name' => 'Rafael Silva',
            'email' => 'rafael@techbelt.io',
            'phone' => '+5521970179121',
            'password' => 'password',
            'role' => [User::ROLE_ADMIN, 'instructor'],
        ]);
        
        $this->command->info("Created Rafael Silva: rafael@techbelt.io / password");

        // 2. Tamires Santos - Admin & Student
        $tamires = User::factory()->create([
            'name' => 'Tamires Santos',
            'email' => 'tamires@techbelt.io',
            'phone' => '+5521970179122',
            'password' => 'password',
            'role' => [User::ROLE_ADMIN, User::ROLE_STUDENT],
        ]);
        
        $this->command->info("Created Tamires Santos: tamires@techbelt.io / password");

        // Create a subscription for Tamires (as a student)
        $tamires->subscriptions()->create([
            'pricing_tier_id' => $pricingTier->id,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'status' => 'active',
        ]);
        
        $this->command->info("Created active subscription for Tamires Santos");

        // 3. Create a test student for onboarding flow
        $testStudent = User::factory()->create([
            'name' => 'Estudante Teste',
            'email' => 'estudante@teste.com',
            'phone' => '+5511999999999',
            'password' => 'password',
            'role' => User::ROLE_STUDENT,
        ]);
        
        $this->command->info("Created test student: estudante@teste.com / password");

        $this->command->info("\n=== CREDENCIAIS PADRÃO ===");
        $this->command->info("Todos os usuários usam senha: 'password'");
        $this->command->info("1. Rafael Silva (Admin & Instructor)");
        $this->command->info("   Email: rafael@techbelt.io");
        $this->command->info("   Telefone: +5521970179121");
        $this->command->info("   Papéis: admin, instructor");
        $this->command->info("");
        $this->command->info("2. Tamires Santos (Admin & Student)");
        $this->command->info("   Email: tamires@techbelt.io");
        $this->command->info("   Telefone: +5521970179122");
        $this->command->info("   Papéis: admin, student");
        $this->command->info("   Possui matrícula ativa: Sim");
        $this->command->info("");
        $this->command->info("3. Estudante Teste (Student)");
        $this->command->info("   Email: estudante@teste.com");
        $this->command->info("   Telefone: +5511999999999");
        $this->command->info("   Papéis: student");
        $this->command->info("");
        $this->command->info("=== PAINÉIS DE ACESSO ===");
        $this->command->info("• Painel Admin: http://localhost:8000/admin");
        $this->command->info("• Painel Staff: http://localhost:8000/staff");
        $this->command->info("• Aplicativo Estudante: http://localhost:8000/app");
    }
}
