<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Quiz: {{ $class->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('teacher.quizzes.store', $class) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="6" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="available_from" value="Available From" />
                                <x-text-input 
                                    id="available_from" 
                                    name="available_from" 
                                    type="datetime-local" 
                                    class="mt-1 block w-full" 
                                    value="{{ old('available_from') }}" 
                                    required 
                                    x-data="{ setMin() { this.$el.min = new Date().toISOString().slice(0, 16); } }" 
                                    x-init="setMin()" 
                                    @change="document.getElementById('due_at').min = $el.value" 
                                />
                                <x-input-error :messages="$errors->get('available_from')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="due_at" value="Due Date & Time" />
                                <x-text-input 
                                    id="due_at" 
                                    name="due_at" 
                                    type="datetime-local" 
                                    class="mt-1 block w-full" 
                                    value="{{ old('due_at') }}" 
                                    required 
                                    x-data="{ setMin() { const availFrom = document.getElementById('available_from').value; this.$el.min = availFrom || new Date().toISOString().slice(0, 16); } }" 
                                    x-init="setMin()" 
                                />
                                <x-input-error :messages="$errors->get('due_at')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button>Create</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
