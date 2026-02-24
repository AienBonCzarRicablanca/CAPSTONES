<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>Join a class using a code, view lessons, submit homework, and take quizzes.</div>
                        <div class="flex gap-4">
                            <a class="text-indigo-600 hover:underline" href="{{ route('student.classes.index') }}">My Classes</a>
                            <a class="text-indigo-600 hover:underline" href="{{ route('student.progress.index') }}">My Progress</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
