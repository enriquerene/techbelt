<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceitar Convite - Scotelaro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Load Filament styles if needed -->
    @filamentStyles
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 dark:text-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Complete seu Cadastro</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Você foi convidado para o Scotelaro como <strong>{{ $invite->role === 'student' ? 'aluno' : ($invite->role === 'staff' ? 'professor' : 'administrador') }}</strong>.</p>
            <p class="text-gray-600 dark:text-gray-300">Telefone: <strong>{{ $invite->phone }}</strong></p>
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

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Criar Conta
            </button>
        </form>
    </div>
    @filamentScripts
</body>
</html>