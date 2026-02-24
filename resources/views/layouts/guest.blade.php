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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .auth-blob-1 {
                position: absolute;
                width: 400px;
                height: 400px;
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                top: -100px;
                left: -100px;
                z-index: 0;
                opacity: 0.9;
            }
            .auth-blob-2 {
                position: absolute;
                width: 300px;
                height: 300px;
                background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
                border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
                bottom: -50px;
                right: -50px;
                z-index: 0;
                opacity: 0.8;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen relative overflow-hidden bg-white flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Decorative Blobs -->
            <div class="auth-blob-1"></div>
            <div class="auth-blob-2"></div>

            <div class="relative z-10">
                <a href="/">
                    <x-application-logo class="w-20 h-20 text-blue-600" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-lg overflow-hidden sm:rounded-lg relative z-10">
                {{ $slot }}
            </div>

            <div class="mt-4 text-sm text-gray-600 relative z-10">
                <a href="/" class="hover:text-blue-600 transition">← Back to Home</a>
            </div>
        </div>
    </body>
</html>
