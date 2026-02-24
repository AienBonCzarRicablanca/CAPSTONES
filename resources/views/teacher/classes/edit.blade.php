<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Class</h2>
                <div class="text-sm text-gray-500">{{ $class->name }}</div>
            </div>
            <a class="text-indigo-600 hover:underline" href="{{ route('teacher.classes.show', $class) }}">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('teacher.classes.update', $class) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" value="Class Name" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $class->name) }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" rows="4">{{ old('description', $class->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_private" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_private', $class->is_private) ? 'checked' : '' }} />
                                <span class="ms-2 text-sm text-gray-700">Private class</span>
                            </label>
                            <div class="text-xs text-gray-500 mt-1">If enabled, this class is marked as private.</div>
                            <x-input-error :messages="$errors->get('is_private')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="photo" value="Class Picture" />
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                                @if($class->photo_path)
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($class->photo_path) }}" alt="Class picture" />
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-200"></div>
                                @endif
                                <input id="photo" name="photo" type="file" accept="image/*" class="block w-full text-sm" />
                            </div>
                            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Save Changes</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
