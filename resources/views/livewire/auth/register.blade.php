<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account. Phone number is required, email is optional.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Phone Number -->
            <div x-data="{
                phoneDisplay: '{{ old('phone') ? \App\Helpers\PhoneNormalizer::formatForDisplay(old('phone')) : '' }}',
                actualPhone: '{{ old('phone', '') }}',
                formatPhone() {
                    // Remove all non-digits
                    let digits = this.phoneDisplay.replace(/\D/g, '');
                    
                    // Format as (XX) XXXXX-XXXX
                    if (digits.length >= 2) {
                        let formatted = '(' + digits.substring(0, 2);
                        if (digits.length > 2) {
                            formatted += ') ' + digits.substring(2, 7);
                            if (digits.length > 7) {
                                formatted += '-' + digits.substring(7, 11);
                            }
                        }
                        this.phoneDisplay = formatted;
                    }
                    
                    // Update actual phone value (digits only)
                    let fullDigits = digits;
                    if (fullDigits.length >= 10) {
                        this.actualPhone = fullDigits;
                    } else {
                        this.actualPhone = '';
                    }
                }
            }" x-init="formatPhone()">
                <!-- Hidden input for the actual phone value that will be submitted -->
                <input type="hidden" name="phone" x-model="actualPhone" />
                
                <!-- Display input for formatting only -->
                <flux:input
                    :label="__('Phone number')"
                    x-model="phoneDisplay"
                    x-on:input="formatPhone()"
                    type="tel"
                    required
                    autocomplete="tel"
                    placeholder="(11) 99999-9999"
                />
            </div>

            <!-- Email Address (Optional) -->
            <flux:input
                name="email"
                :label="__('Email address (optional)')"
                :value="old('email')"
                type="email"
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
