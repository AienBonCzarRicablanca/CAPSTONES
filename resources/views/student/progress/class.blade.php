<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Progress: {{ $class->name }}</h2>
                <div class="text-sm text-gray-500">Assignments + quizzes summary</div>
            </div>
            <a class="text-indigo-600 hover:underline" href="{{ route('student.progress.index') }}">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <h3 class="font-semibold">Assignments</h3>
                    <table class="min-w-full text-sm mt-4">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Title</th>
                                <th class="py-2">Submitted</th>
                                <th class="py-2">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $a)
                                @php $sub = $a->submissions->first(); @endphp
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $a->title }}</td>
                                    <td class="py-3">{{ $sub?->submitted_at ? $sub->submitted_at->format('M d, Y') : '—' }}</td>
                                    <td class="py-3">{{ $sub?->grade ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-gray-500">No assignments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <h3 class="font-semibold">Quizzes</h3>
                    <table class="min-w-full text-sm mt-4">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Title</th>
                                <th class="py-2">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quizzes as $q)
                                @php
                                    $max = $q->questions->sum('points');
                                    $score = $q->questions->sum(function ($qq) use ($answers) {
                                        return (int) ($answers[$qq->id]->awarded_points ?? 0);
                                    });
                                @endphp
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $q->title }}</td>
                                    <td class="py-3">{{ $score }} / {{ $max }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-6 text-gray-500">No quizzes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
