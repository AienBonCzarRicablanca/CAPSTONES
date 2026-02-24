<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Join Class') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('student.classes.join.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="join_code" value="Class Join Code" />
                            <x-text-input id="join_code" name="join_code" type="text" class="mt-1 block w-full" value="{{ old('join_code') }}" required autofocus />
                            <x-input-error :messages="$errors->get('join_code')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Join</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
