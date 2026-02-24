<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quizzes: {{ $class->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Title</th>
                                <th class="py-2">Due</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quizzes as $q)
                                @php $submitted = ($q->submissions ?? collect())->isNotEmpty(); @endphp
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $q->title }}</td>
                                    <td class="py-3 text-gray-600">{{ $q->due_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="py-3">
                                        @if($submitted)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800">Completed</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">Not started</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($submitted)
                                            <a class="text-indigo-600 hover:underline" href="{{ route('student.quizzes.result', [$class, $q]) }}">Review</a>
                                        @else
                                            <a class="text-indigo-600 hover:underline" href="{{ route('student.quizzes.take', [$class, $q]) }}">Take</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-gray-500">No quizzes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $quizzes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
