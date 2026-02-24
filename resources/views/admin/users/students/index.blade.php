<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Students</h2>
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest">Add Student</a>
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
                                    @forelse($students as $s)
                                        <tr class="border-t">
                                            <td class="py-3">{{ $s->name }}</td>
                                            <td class="py-3 text-gray-600">{{ $s->email }}</td>
                                            <td class="py-3 flex gap-3">
                                                <a class="text-indigo-600 hover:underline" href="{{ route('admin.students.edit', $s) }}">Edit</a>
                                                <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Delete this student?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-6 text-gray-500">No students.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-4">{{ $students->links() }}</div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
