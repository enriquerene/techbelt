<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name', 'Tech Belt') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h1>
            <p class="text-gray-600 mb-6">Welcome to your dashboard, {{ auth()->user()->name ?? 'User' }}!</p>
            
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h2 class="font-semibold text-blue-900">Quick Stats</h2>
                    <p class="text-blue-700">You are logged in successfully.</p>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h2 class="font-semibold text-green-900">Navigation</h2>
                    <ul class="mt-2 space-y-2">
                        <li><a href="/app" class="text-green-700 hover:text-green-900">Go to Student App →</a></li>
                        <li><a href="/admin" class="text-green-700 hover:text-green-900">Go to Admin Panel →</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-red-700 hover:text-red-900">Logout →</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
