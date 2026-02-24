<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Library Categories</h2>
            <a href="{{ route('admin.library.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest">Create</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2">Name</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $c)
                                        <tr class="border-t">
                                            <td class="py-3 font-medium">{{ $c->name }}</td>
                                            <td class="py-3">
                                                <a class="text-indigo-600 hover:underline" href="{{ route('admin.library.categories.edit', $c) }}">Edit</a>
                                                <span class="text-gray-300 mx-2">|</span>
                                                <form class="inline" method="POST" action="{{ route('admin.library.categories.destroy', $c) }}" onsubmit="return confirm('Delete category?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="py-6 text-gray-500">No categories.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-4">{{ $categories->links() }}</div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
