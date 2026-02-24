<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Assignment: {{ $class->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('teacher.assignments.store', $class) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label value="Type" />
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="HOMEWORK">Homework</option>
                                <option value="ACTIVITY">Activity</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="6" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <x-input-label for="due_at" value="Due Date" />
                            <x-text-input id="due_at" name="due_at" type="date" class="mt-1 block w-full" value="{{ old('due_at') }}" required />
                            <x-input-error :messages="$errors->get('due_at')" class="mt-2" />
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="allow_late" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('allow_late') ? 'checked' : '' }} />
                                <span class="ms-2 text-sm text-gray-700">Allow late turn in</span>
                            </label>
                            <div class="text-xs text-gray-500 mt-1">If enabled, students can submit after the due date and it will be marked as late.</div>
                            <x-input-error :messages="$errors->get('allow_late')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="attachment" value="Attachment (optional)" />
                            <input id="attachment" name="attachment" type="file" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
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
