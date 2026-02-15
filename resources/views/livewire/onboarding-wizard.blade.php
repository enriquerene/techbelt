<div class="max-w-4xl mx-auto p-6">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            @foreach(['Modalidades', 'Turmas', 'Cálculo', 'Pagamento'] as $index => $label)
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $step > $index + 1 ? 'bg-green-500 text-white' : ($step == $index + 1 ? 'bg-indigo-600 text-black' : 'bg-gray-200 text-gray-600') }}">
                        {{ $index + 1 }}
                    </div>
                    <span class="mt-2 text-sm font-medium {{ $step >= $index + 1 ? 'text-indigo-600' : 'text-gray-500' }}">{{ $label }}</span>
                </div>
                @if($index < 3)
                    <div class="flex-1 h-1 mx-4 {{ $step > $index + 1 ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Step Content -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        @if($step == 1)
            <h2 class="text-2xl font-bold mb-4">Selecione as Modalidades</h2>
            <p class="text-gray-600 mb-6">Escolha as modalidades que você deseja praticar. Clique para selecionar/desselecionar.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($modalities as $modality)
                    <label class="border-2 rounded-xl p-5 cursor-pointer transition-all duration-200 hover:border-indigo-500 hover:shadow-md {{ in_array($modality->id, $selectedModalities) ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-200' }}">
                        <input type="checkbox" wire:model="selectedModalities" value="{{ $modality->id }}" class="hidden">
                        
                        @if($modality->image)
                            <div class="mb-4 overflow-hidden rounded-lg">
                                <img src="{{ asset('storage/' . $modality->image) }}" alt="{{ $modality->name }}" class="w-full h-40 object-cover">
                            </div>
                        @else
                            <div class="mb-4 bg-gray-100 rounded-lg h-40 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="font-bold text-lg text-gray-800">{{ $modality->name }}</div>
                        <div class="text-sm text-gray-600 mt-2">{{ $modality->description ?? 'Sem descrição' }}</div>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs font-medium px-3 py-1 rounded-full {{ in_array($modality->id, $selectedModalities) ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ in_array($modality->id, $selectedModalities) ? 'Selecionada' : 'Clique para selecionar' }}
                            </span>
                            @if(in_array($modality->id, $selectedModalities))
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-blue-800">Selecione pelo menos uma modalidade para continuar.</span>
                </div>
            </div>
        @elseif($step == 2)
            <h2 class="text-2xl font-bold mb-4">Selecione as Turmas</h2>
            <p class="text-gray-600 mb-6">Escolha pelo menos uma turma de cada modalidade selecionada.</p>
            
            @php
                // Group classes by modality
                $groupedClasses = $classes->groupBy('modality_id');
                $selectedModalityNames = $modalities->whereIn('id', $selectedModalities)->pluck('name', 'id');
            @endphp
            
            @if(count($classes) > 0)
                <div class="space-y-8">
                    @foreach($groupedClasses as $modalityId => $modalityClasses)
                        <div class="border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-3 h-3 rounded-full bg-indigo-500 mr-3"></div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $selectedModalityNames[$modalityId] ?? 'Modalidade' }}</h3>
                                <span class="ml-3 text-sm font-medium px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full">
                                    {{ $modalityClasses->count() }} turma(s) disponível(is)
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($modalityClasses as $class)
                                    <label class="border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-indigo-500 hover:shadow-sm {{ in_array($class->id, $selectedClasses) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                        <input type="checkbox" wire:model="selectedClasses" value="{{ $class->id }}" class="hidden">
                                        
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $class->name }}</div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $class->schedule }}
                                                </div>
                                                @if($class->instructor)
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        {{ $class->instructor->name }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if(in_array($class->id, $selectedClasses))
                                                <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-3 text-xs font-medium px-2 py-1 rounded-full {{ in_array($class->id, $selectedClasses) ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ in_array($class->id, $selectedClasses) ? 'Selecionada' : 'Clique para selecionar' }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-blue-800">É necessário selecionar pelo menos uma turma de cada modalidade escolhida.</span>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Nenhuma turma disponível para as modalidades selecionadas.</p>
                    <button wire:click="back" class="mt-4 px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Voltar e escolher outras modalidades
                    </button>
                </div>
            @endif
        @elseif($step == 3)
            <h2 class="text-2xl font-bold mb-4">Checkout</h2>
            <p class="text-gray-600 mb-6">Confira suas escolhas e o valor do plano.</p>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Selected Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Resumo das Escolhas</h3>
                        
                        @php
                            $selectedModalityNames = $modalities->whereIn('id', $selectedModalities)->pluck('name', 'id');
                            $selectedClassDetails = $classes->whereIn('id', $selectedClasses);
                            $groupedSelectedClasses = $selectedClassDetails->groupBy('modality_id');
                        @endphp
                        
                        <div class="space-y-6">
                            @foreach($groupedSelectedClasses as $modalityId => $modalityClasses)
                                <div class="border border-gray-100 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <div class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></div>
                                        <h4 class="font-bold text-gray-800">{{ $selectedModalityNames[$modalityId] ?? 'Modalidade' }}</h4>
                                        <span class="ml-2 text-sm text-gray-600">({{ $modalityClasses->count() }} turma(s))</span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        @foreach($modalityClasses as $class)
                                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                                <div>
                                                    <div class="font-medium text-gray-800">{{ $class->name }}</div>
                                                    <div class="text-sm text-gray-600">{{ $class->schedule }}</div>
                                                </div>
                                                @if($class->instructor)
                                                    <div class="text-sm text-gray-600">{{ $class->instructor->name }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Pricing Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Resumo Financeiro</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total de turmas:</span>
                                <span class="font-bold text-gray-900">{{ count($selectedClasses) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Plano aplicável:</span>
                                <span class="font-bold text-gray-900">{{ $pricingTier ? $pricingTier->name : 'Nenhum plano encontrado' }}</span>
                            </div>
                            
                            @if($pricingTier)
                                <div class="pt-4 border-t border-gray-300">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-700">Valor do plano:</span>
                                        <span class="text-lg font-bold text-gray-900">R$ {{ number_format($pricingTier->price, 2, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600">
                                        @if($pricingTier->class_count == 0)
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Plano ilimitado
                                            </span>
                                        @elseif($pricingTier->class_count > count($selectedClasses))
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                                Capacidade para {{ $pricingTier->class_count }} turmas (você selecionou {{ count($selectedClasses) }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Plano ideal para {{ $pricingTier->class_count }} turmas
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <div class="pt-4 border-t border-gray-300">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-900">Total mensal:</span>
                                    <span class="text-3xl font-bold text-green-600">R$ {{ number_format($total, 2, ',', '.') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Valor recorrente mensalmente até cancelamento.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Como o plano é escolhido:</p>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    <li>Se você escolheu {{ count($selectedClasses) }} turmas, buscamos um plano para {{ count($selectedClasses) }} turmas</li>
                                    <li>Se não existe, buscamos para {{ count($selectedClasses) + 1 }} turmas</li>
                                    <li>Se ainda não existe, usamos o plano ilimitado</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($step == 4)
            <h2 class="text-2xl font-bold mb-4">Pagamento</h2>
            <p class="text-gray-600 mb-6">Finalize sua inscrição realizando o pagamento.</p>
            <div class="bg-gray-50 p-6 rounded-lg">
                <p class="mb-4">Integração com gateway de pagamento (ex: MercadoPago, Asaas) será implementada aqui.</p>
                <p class="text-sm text-gray-500">Para fins de demonstração, clique em "Finalizar" para criar a assinatura.</p>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            @if($step > 1)
                <button wire:click="back" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Voltar
                </button>
            @else
                <div></div>
            @endif

            @if($step < 4)
                @if($step == 1 && empty($selectedModalities))
                    <button class="px-6 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed" disabled>
                        Próximo
                    </button>
                @elseif($step == 2 && empty($selectedClasses))
                    <button class="px-6 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed" disabled>
                        Próximo
                    </button>
                @elseif($step == 3 && !$pricingTier)
                    <button class="px-6 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed" disabled>
                        Próximo
                    </button>
                @else
                    <button wire:click="proceed" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200">
                        Próximo
                    </button>
                @endif
            @else
                <button wire:click="proceed" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                    Finalizar Inscrição
                </button>
            @endif
        </div>
    </div>
</div>
