<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Library Item</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.library.items.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label value="Title" />
                            <x-text-input name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Category" />
                                <select name="library_category_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">—</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Language" />
                                <x-text-input name="language" class="mt-1 block w-full" value="{{ old('language', 'English') }}" required />
                            </div>
                            <div>
                                <x-input-label value="Difficulty" />
                                <x-text-input name="difficulty" class="mt-1 block w-full" value="{{ old('difficulty', 'Beginner') }}" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Text Content" />
                            <textarea name="text_content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('text_content') }}</textarea>
                        </div>
                        <div>
                            <x-input-label value="Audio File (optional)" />
                            <input type="file" name="audio" class="mt-1 block w-full" />
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
