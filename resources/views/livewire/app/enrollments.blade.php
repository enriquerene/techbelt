<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Suas Inscrições</h2>

        @if($enrollments->count() > 0)
            <div class="space-y-3">
                @foreach($enrollments as $enrollment)
                    <div class="bg-white dark:bg-slate-900 rounded-lg p-4 border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $enrollment->gymClass->name }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $enrollment->gymClass->modality->name }}</p>
                            </div>
                            <span class="inline-block px-3 py-1 {{ $enrollment->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300' }} rounded-full text-xs font-medium">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>

                        @if($enrollment->gymClass->instructor)
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                                <strong>Professor:</strong> {{ $enrollment->gymClass->instructor->name }}
                            </p>
                        @endif

                        @if($enrollment->enrolled_at)
                            <p class="text-xs text-slate-500 dark:text-slate-500 mb-3">
                                <strong>Inscrito em:</strong> {{ $enrollment->enrolled_at->format('d/m/Y') }}
                            </p>
                        @endif

                        <div class="flex gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                            <button class="flex-1 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white rounded-lg font-medium text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                Detalhes
                            </button>
                            @if($enrollment->status === 'active')
                                <button class="flex-1 px-3 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg font-medium text-sm hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-slate-600 dark:text-slate-400 mb-4">Você ainda não está inscrito em nenhuma turma</p>
                <a href="{{ route('app.classes') }}" wire:navigate class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    Explorar Turmas
                </a>
            </div>
        @endif
    </div>
</div>
