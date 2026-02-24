<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Library Item</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.library.items.update', $item) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label value="Title" />
                            <x-text-input name="title" class="mt-1 block w-full" value="{{ old('title', $item->title) }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Category" />
                                <select name="library_category_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">—</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" @selected((string) $item->library_category_id === (string) $c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Language" />
                                <x-text-input name="language" class="mt-1 block w-full" value="{{ old('language', $item->language) }}" required />
                            </div>
                            <div>
                                <x-input-label value="Difficulty" />
                                <x-text-input name="difficulty" class="mt-1 block w-full" value="{{ old('difficulty', $item->difficulty) }}" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Text Content" />
                            <textarea name="text_content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('text_content', $item->text_content) }}</textarea>
                        </div>
                        <div>
                            <x-input-label value="Audio File (optional)" />
                            @if($item->audio_path)
                                <div class="text-sm text-gray-600">Current audio: {{ $item->audio_path }}</div>
                                <label class="inline-flex items-center gap-2 mt-2 text-sm">
                                    <input type="checkbox" name="remove_audio" value="1" />
                                    <span>Remove audio</span>
                                </label>
                            @endif
                            <input type="file" name="audio" class="mt-2 block w-full" />
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button>Save</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
