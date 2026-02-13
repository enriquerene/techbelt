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
            <p class="text-gray-600 mb-6">Escolha as modalidades que você deseja praticar.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($modalities as $modality)
                    <label class="border rounded-lg p-4 cursor-pointer hover:border-indigo-500 {{ in_array($modality->id, $selectedModalities) ? 'border-indigo-500 bg-indigo-50' : '' }}">
                        <input type="checkbox" wire:model="selectedModalities" value="{{ $modality->id }}" class="hidden">
                        <div class="font-semibold">{{ $modality->name }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ $modality->description ?? 'Sem descrição' }}</div>
                    </label>
                @endforeach
            </div>
        @elseif($step == 2)
            <h2 class="text-2xl font-bold mb-4">Selecione as Turmas</h2>
            <p class="text-gray-600 mb-6">Com base nas modalidades, escolha as turmas disponíveis.</p>
            @if(count($classes) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($classes as $class)
                        <label class="border rounded-lg p-4 cursor-pointer hover:border-indigo-500 {{ in_array($class->id, $selectedClasses) ? 'border-indigo-500 bg-indigo-50' : '' }}">
                            <input type="checkbox" wire:model="selectedClasses" value="{{ $class->id }}" class="hidden">
                            <div class="font-semibold">{{ $class->name }}</div>
                            <div class="text-sm text-gray-500">{{ $class->modality->name }} • {{ $class->schedule }}</div>
                            <div class="text-sm text-gray-500">Instrutor: {{ $class->instructor->name ?? 'N/A' }}</div>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Nenhuma turma disponível para as modalidades selecionadas.</p>
            @endif
        @elseif($step == 3)
            <h2 class="text-2xl font-bold mb-4">Cálculo do Valor</h2>
            <p class="text-gray-600 mb-6">Confira o valor com base nas turmas selecionadas.</p>
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg">Quantidade de turmas:</span>
                    <span class="text-xl font-bold">{{ count($selectedClasses) }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg">Plano aplicável:</span>
                    <span class="text-xl font-bold">{{ $pricingTier ? $pricingTier->name : 'Nenhum' }}</span>
                </div>
                <div class="flex justify-between items-center border-t pt-4">
                    <span class="text-2xl font-bold">Total mensal:</span>
                    <span class="text-3xl font-bold text-green-600">R$ {{ number_format($total, 2, ',', '.') }}</span>
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
                <button wire:click="back" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Voltar
                </button>
            @else
                <div></div>
            @endif

            @if($step < 4)
                <button wire:click="proceed" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Próximo
                </button>
            @else
                <button wire:click="proceed" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Finalizar Inscrição
                </button>
            @endif
        </div>
    </div>
</div>
