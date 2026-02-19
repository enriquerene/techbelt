<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your phone number and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

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
                    autofocus
                    autocomplete="tel"
                    placeholder="(11) 99999-9999"
                />
            </div>

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
