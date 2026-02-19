<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceitar Convite - Tech Belt</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Load Filament styles if needed -->
    @filamentStyles
</head>
<body class="bg-black min-h-screen flex items-center justify-center p-4 dark">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 dark:text-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Complete seu Cadastro</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Você foi convidado para o Tech Belt como <strong>{{ $invite->role === 'student' ? 'aluno' : ($invite->role === 'staff' ? 'professor' : 'administrador') }}</strong>.</p>
            <p class="text-gray-600 dark:text-gray-300">Telefone: <strong>{{ \App\Helpers\PhoneNormalizer::formatForDisplay($invite->phone) }}</strong></p>
            @if($invite->name)
                <p class="text-gray-600 dark:text-gray-300">Nome: <strong>{{ $invite->name }}</strong></p>
            @endif
        </div>

        <form method="POST" action="{{ route('invite.accept', $invite->token) }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                       placeholder="Digite sua senha">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                       placeholder="Confirme sua senha">
            </div>

            <div class="mb-6 mt-2">
                <div class="flex items-start">
                    <input type="checkbox" id="terms" name="terms" value="1" required
                           class="h-5 w-5 mt-0.5 text-indigo-600 focus:ring-2 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                    <label for="terms" class="ml-3 block text-sm text-gray-700 dark:text-gray-300 px-3">
                        Eu concordo com os <a href="#" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Termos de Uso</a> e <a href="#" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Política de Privacidade</a>.
                    </label>
                </div>
                @error('terms')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Criar Conta
            </button>
        </form>
    </div>
    @filamentScripts
</body>
</html>