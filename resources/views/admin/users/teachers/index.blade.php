<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Teachers</h2>
            <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest">Add Teacher</a>
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
                                        <th class="py-2">Email</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachers as $t)
                                        <tr class="border-t">
                                            <td class="py-3">{{ $t->name }}</td>
                                            <td class="py-3 text-gray-600">{{ $t->email }}</td>
                                            <td class="py-3 flex gap-3">
                                                <a class="text-indigo-600 hover:underline" href="{{ route('admin.teachers.edit', $t) }}">Edit</a>
                                                <form method="POST" action="{{ route('admin.teachers.destroy', $t) }}" onsubmit="return confirm('Delete this teacher?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-6 text-gray-500">No teachers.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-4">{{ $teachers->links() }}</div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
