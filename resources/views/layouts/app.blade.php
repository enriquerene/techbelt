<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tech Belt') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @filamentStyles
</head>
<body class="bg-slate-50 dark:bg-slate-950 antialiased">
    <div class="min-h-screen flex flex-col pb-20 md:pb-0">
        <!-- Mobile Header -->
        <header class="sticky top-0 z-40 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 md:border-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">
                        {{ config('app.name', 'Tech Belt') }}
                    </h1>
                    <div class="flex items-center gap-2">
                        <!-- Notifications Bell -->
                        <button class="relative p-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">0</span>
                        </button>
                        
                        <!-- User Menu -->
                        <x-desktop-user-menu />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </main>

        <!-- Mobile Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 md:hidden">
            <div class="grid grid-cols-5 gap-1 px-2 py-2">
                <a href="{{ route('app.home') }}" wire:navigate class="flex flex-col items-center justify-center py-2 px-1 rounded-lg text-xs font-medium transition {{ request()->routeIs('app.home') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.707.707a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span>Home</span>
                </a>
                
                <a href="{{ route('app.classes') }}" wire:navigate class="flex flex-col items-center justify-center py-2 px-1 rounded-lg text-xs font-medium transition {{ request()->routeIs('app.classes') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                    </svg>
                    <span>Classes</span>
                </a>
                
                <a href="{{ route('app.enrollments') }}" wire:navigate class="flex flex-col items-center justify-center py-2 px-1 rounded-lg text-xs font-medium transition {{ request()->routeIs('app.enrollments') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Enrolls</span>
                </a>
                
                <a href="{{ route('app.progress') }}" wire:navigate class="flex flex-col items-center justify-center py-2 px-1 rounded-lg text-xs font-medium transition {{ request()->routeIs('app.progress') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Progress</span>
                </a>
                
                <a href="{{ route('app.profile') }}" wire:navigate class="flex flex-col items-center justify-center py-2 px-1 rounded-lg text-xs font-medium transition {{ request()->routeIs('app.profile') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
            </div>
        </nav>
    </div>

    @livewireScripts
    @fluxScripts
</body>
</html>
