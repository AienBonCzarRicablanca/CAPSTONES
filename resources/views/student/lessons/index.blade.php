<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lessons: {{ $class->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Title</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lessons as $lesson)
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $lesson->title }}</td>
                                    <td class="py-3"><a class="text-indigo-600 hover:underline" href="{{ route('student.lessons.show', [$class, $lesson]) }}">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-6 text-gray-500">No lessons available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $lessons->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
