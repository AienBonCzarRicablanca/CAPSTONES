<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .blob-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            top: -100px;
            left: -100px;
            z-index: 0;
            opacity: 0.9;
        }
        .blob-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
            top: 200px;
            right: -50px;
            z-index: 0;
            opacity: 0.8;
        }
        .blob-3 {
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
            border-radius: 50% 50% 30% 70% / 30% 70% 70% 30%;
            bottom: 100px;
            right: 200px;
            z-index: 0;
            opacity: 0.7;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900">
    <div class="relative min-h-screen overflow-hidden bg-white">
        <!-- Decorative Blobs -->
        <div class="blob-1"></div>
        <div class="blob-2"></div>
        <div class="blob-3"></div>

        <!-- Navigation -->
        <nav class="relative z-10 px-6 py-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <x-application-logo class="h-8 w-8 text-blue-600" />
                    <span class="text-xl font-bold text-gray-800">{{ config('app.name') }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2 rounded-full bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 font-medium hover:text-blue-600 transition">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Sign Up</a>
                        <a href="{{ route('teacher-applications.create') }}" class="px-4 py-2 text-blue-600 border border-blue-600 rounded-full font-medium hover:bg-blue-50 transition">Apply as Teacher</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative z-10 max-w-7xl mx-auto px-6 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        The Best Platform For <span class="text-blue-600">Enhancing Skills</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        Seamless access to learning resources, assignments, and communication tools for both teachers and students. Experience modern education with our comprehensive learning management system.
                    </p>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('register') }}" class="px-8 py-3 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg">
                            Get Started
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-3 rounded-full border-2 border-blue-600 text-blue-600 font-semibold hover:bg-blue-700 transition">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="w-80 h-80 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-48 h-48 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Feature Icons -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-20">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-white border-2 border-blue-100 flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Students</h3>
                    <p class="text-sm text-gray-600 mt-1">Join classes & submit work</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-white border-2 border-blue-100 flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Ideas</h3>
                    <p class="text-sm text-gray-600 mt-1">Interactive learning</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-white border-2 border-blue-100 flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Library</h3>
                    <p class="text-sm text-gray-600 mt-1">Digital resources</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-blue-600 flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Teachers</h3>
                    <p class="text-sm text-gray-600 mt-1">Create & manage</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
