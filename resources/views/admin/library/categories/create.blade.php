<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Category</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <form method="POST" action="{{ route('admin.library.categories.store') }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <x-input-label value="Name" />
                                        <x-text-input name="name" class="mt-1 block w-full" value="{{ old('name') }}" required />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
                                    <div class="flex justify-end">
                                        <x-primary-button>Create</x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
