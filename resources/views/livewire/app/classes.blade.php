<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Turmas Disponíveis</h2>
        
        @if($classes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($classes as $class)
                    <div class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-800 hover:shadow-lg transition">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-24"></div>
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 dark:text-white mb-1">{{ $class->name ?? 'Sem nome' }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                                {{ $class->modality->name }}
                            </p>
                            
                            @if($class->instructor)
                                <p class="text-xs text-slate-500 dark:text-slate-500 mb-3">
                                    <strong>Professor:</strong> {{ $class->instructor->name }}
                                </p>
                            @endif

                            @if($class->capacity)
                                <p class="text-xs text-slate-500 dark:text-slate-500 mb-3">
                                    <strong>Capacidade:</strong> {{ $class->capacity }} alunos
                                </p>
                            @endif

                            <div class="flex gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                                <button class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition">
                                    Inscrever-se
                                </button>
                                <button class="flex-1 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white rounded-lg font-medium text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    Ver mais
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-lg p-8 text-center">
                <p class="text-slate-600 dark:text-slate-400">Nenhuma turma disponível no momento</p>
            </div>
        @endif
    </div>
</div>
