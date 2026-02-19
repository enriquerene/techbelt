<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Seu Perfil</h2>

        <!-- Profile Card -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 h-24"></div>
            
            <div class="px-6 pb-6 -mt-12 relative">
                <!-- Avatar -->
                <div class="inline-block">
                    <div class="w-24 h-24 bg-blue-600 rounded-lg flex items-center justify-center text-white text-2xl font-bold border-4 border-white dark:border-slate-900">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>

                <div class="mt-4">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
                    @if($user->email)
                        <p class="text-slate-600 dark:text-slate-400">{{ $user->email }}</p>
                    @endif
                    @if($user->phone)
                        <p class="text-slate-600 dark:text-slate-400">{{ \App\Helpers\PhoneNormalizer::formatForDisplay($user->phone) }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-lg p-4 border border-slate-200 dark:border-slate-800">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Membro desde</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-lg p-4 border border-slate-200 dark:border-slate-800">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Status</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">Ativo</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <button class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                Editar Perfil
            </button>
            <button class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white rounded-lg font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                Alterar Senha
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                    Sair
                </button>
            </form>
        </div>
    </div>
</div>
