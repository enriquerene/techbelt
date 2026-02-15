<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Modality;
use App\Models\GymClass;
use App\Models\PricingTier;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class OnboardingWizard extends Component
{
    public $step = 1;
    public $selectedModalities = [];
    public $selectedClasses = [];
    public $pricingTier = null;
    public $total = 0;
    public $paymentMethod = 'credit_card';
    public $cardNumber = '';
    public $cardExpiry = '';
    public $cardCvc = '';
    public $isProcessing = false;

    protected $listeners = ['proceed', 'back'];

    public function mount()
    {
        // If user already has active subscription, redirect
        $user = auth()->user();
        if ($user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->exists()) {
            return redirect()->route('app.home');
        }
    }

    public function render()
    {
        $modalities = Modality::with('classes')->get();
        $classes = GymClass::whereIn('modality_id', $this->selectedModalities)->get();
        $pricingTiers = PricingTier::all();

        return view('livewire.onboarding-wizard', compact('modalities', 'classes', 'pricingTiers'));
    }

    public function updatedSelectedModalities()
    {
        $this->selectedClasses = [];
        $this->calculatePrice();
    }

    public function updatedSelectedClasses()
    {
        $this->calculatePrice();
    }

    public function calculatePrice()
    {
        $classCount = count($this->selectedClasses);
        
        // Find pricing tier with exact class count
        $this->pricingTier = PricingTier::where('class_count', $classCount)->first();
        
        // If no exact match, find tier with class_count = classCount + 1
        if (!$this->pricingTier) {
            $this->pricingTier = PricingTier::where('class_count', $classCount + 1)->first();
        }
        
        // If still no match, find unlimited tier (class_count = 0)
        if (!$this->pricingTier) {
            $this->pricingTier = PricingTier::where('class_count', 0)->first();
        }
        
        $this->total = $this->pricingTier ? $this->pricingTier->price : 0;
    }

    public function proceed()
    {
        // Validate current step before proceeding
        if (!$this->validateStep()) {
            return;
        }

        if ($this->step < 4) {
            $this->step++;
        } else {
            // Step 4 is payment, process it
            $this->processPayment();
        }
    }

    private function validateStep(): bool
    {
        if ($this->step === 1 && empty($this->selectedModalities)) {
            $this->addError('selectedModalities', 'Por favor, selecione pelo menos uma modalidade.');
            return false;
        }

        if ($this->step === 2) {
            if (empty($this->selectedClasses)) {
                $this->addError('selectedClasses', 'Por favor, selecione pelo menos uma turma.');
                return false;
            }
            
            // Check if at least one class from each selected modality is chosen
            $selectedClasses = GymClass::whereIn('id', $this->selectedClasses)
                ->pluck('modality_id')
                ->unique()
                ->toArray();
            
            foreach ($this->selectedModalities as $modalityId) {
                if (!in_array($modalityId, $selectedClasses)) {
                    $this->addError('selectedClasses', 'Por favor, selecione pelo menos uma turma de cada modalidade escolhida.');
                    return false;
                }
            }
        }

        if ($this->step === 3 && !$this->pricingTier) {
            $this->addError('pricingTier', 'Por favor, selecione um plano de preços.');
            return false;
        }

        if ($this->step === 4) {
            // Simple payment validation
            if ($this->paymentMethod === 'credit_card') {
                if (strlen($this->cardNumber) < 16) {
                    $this->addError('cardNumber', 'Por favor, insira um número de cartão válido.');
                    return false;
                }
                if (empty($this->cardExpiry)) {
                    $this->addError('cardExpiry', 'Por favor, insira a data de validade do cartão.');
                    return false;
                }
                if (strlen($this->cardCvc) < 3) {
                    $this->addError('cardCvc', 'Por favor, insira o CVC do cartão.');
                    return false;
                }
            }
        }

        return true;
    }

    public function processPayment()
    {
        $this->isProcessing = true;
        
        // Mock payment processing delay
        sleep(2);
        
        // Simulate successful payment
        $paymentSuccess = true; // Mock always succeeds
        
        if ($paymentSuccess) {
            $this->createSubscription();
        } else {
            $this->addError('payment', 'Payment failed. Please try again.');
            $this->isProcessing = false;
        }
    }

    public function back()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    private function createSubscription()
    {
        $user = auth()->user();
        
        // Create subscription
        $subscription = $user->subscriptions()->create([
            'pricing_tier_id' => $this->pricingTier->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        // Create enrollments for selected classes
        foreach ($this->selectedClasses as $classId) {
            $user->enrollments()->create([
                'class_id' => $classId,
                'subscription_id' => $subscription->id,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
        }

        $this->isProcessing = false;
        
        // Redirect to app with success message
        return redirect()->route('app.home')->with('success', 'Subscription created successfully! Welcome to Scotelaro!');
    }
}
