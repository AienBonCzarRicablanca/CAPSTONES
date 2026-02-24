<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Progress</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Class</th>
                                <th class="py-2">Teacher</th>
                                <th class="py-2">Assignments</th>
                                <th class="py-2">Quizzes</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $class)
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $class->name }}</td>
                                    <td class="py-3 text-gray-600">{{ $class->teacher?->name ?? '—' }}</td>
                                    <td class="py-3">{{ $class->assignments_count }}</td>
                                    <td class="py-3">{{ $class->quizzes_count }}</td>
                                    <td class="py-3"><a class="text-indigo-600 hover:underline" href="{{ route('student.progress.class', $class) }}">View</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-gray-500">No classes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $classes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
