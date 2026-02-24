<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php
            $user = auth()->user();
            $isAdmin = $user?->isAdmin();
            $isTeacher = $user?->isTeacher();
            $isStudent = $user?->isStudent();
        @endphp

        <div x-data="{ 
                sidebarOpen: window.innerWidth >= 768,
                userManagementOpen: {{ request()->routeIs('admin.teachers.*') || request()->routeIs('admin.students.*') ? 'true' : 'false' }},
                libraryAdminOpen: {{ request()->routeIs('admin.library.*') ? 'true' : 'false' }},
                classesOpen: {{ request()->routeIs('*.classes.*') || request()->routeIs('*.lessons.*') || request()->routeIs('*.assignments.*') || request()->routeIs('*.quizzes.*') ? 'true' : 'false' }}
             }" 
             @resize.window="sidebarOpen = window.innerWidth >= 768"
             class="flex h-screen bg-gray-100">
            
            @include('layouts.navigation')
            
            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Mobile Header -->
                <header class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                    <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-lg font-semibold text-gray-900">LMS</span>
                    <div class="w-6"></div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset
                    
                    {{ $slot }}
                </main>
            </div>

            <!-- Sidebar Overlay for Mobile -->
            <div 
                x-show="sidebarOpen && window.innerWidth < 768" 
                @click="sidebarOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
                x-cloak
            ></div>
        </div>

        <style>
            [x-cloak] { display: none !important; }
        </style>

        @stack('scripts')
    </body>
</html>
