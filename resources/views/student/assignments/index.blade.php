<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assignments: {{ $class->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Title</th>
                                <th class="py-2">Type</th>
                                <th class="py-2">Due</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $a)
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $a->title }}</td>
                                    <td class="py-3 text-gray-600">{{ $a->type }}</td>
                                    <td class="py-3 text-gray-600">{{ $a->due_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="py-3"><a class="text-indigo-600 hover:underline" href="{{ route('student.assignments.show', [$class, $a]) }}">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-gray-500">No assignments.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $assignments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
