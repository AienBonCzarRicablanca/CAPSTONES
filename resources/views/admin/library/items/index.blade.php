<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Library Items</h2>
            <a href="{{ route('admin.library.items.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest">Create</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2">Title</th>
                                        <th class="py-2">Category</th>
                                        <th class="py-2">Language</th>
                                        <th class="py-2">Difficulty</th>
                                        <th class="py-2">Audio</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $i)
                                        <tr class="border-t">
                                            <td class="py-3 font-medium">{{ $i->title }}</td>
                                            <td class="py-3 text-gray-600">{{ $i->category?->name ?? '—' }}</td>
                                            <td class="py-3">{{ $i->language }}</td>
                                            <td class="py-3">{{ $i->difficulty }}</td>
                                            <td class="py-3">{{ $i->audio_path ? 'Yes' : 'No' }}</td>
                                            <td class="py-3">
                                                <a class="text-indigo-600 hover:underline" href="{{ route('admin.library.items.edit', $i) }}">Edit</a>
                                                <span class="text-gray-300 mx-2">|</span>
                                                <form class="inline" method="POST" action="{{ route('admin.library.items.destroy', $i) }}" onsubmit="return confirm('Delete item?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="py-6 text-gray-500">No items.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-4">{{ $items->links() }}</div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
