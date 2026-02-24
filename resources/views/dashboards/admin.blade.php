<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm text-gray-500">Total Users</div>
                            <div class="text-3xl font-semibold">{{ $stats['users'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm text-gray-500">Teachers</div>
                            <div class="text-3xl font-semibold">{{ $stats['teachers'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm text-gray-500">Students</div>
                            <div class="text-3xl font-semibold">{{ $stats['students'] ?? 0 }}</div>
                        </div>
                    </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Manage users, library content, teacher verification requests, and system logs.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
