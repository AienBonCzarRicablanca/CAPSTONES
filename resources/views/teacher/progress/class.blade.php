<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Progress: {{ $class->name }}</h2>
                <div class="text-sm text-gray-500">Assignments: {{ $assignmentsTotal }} • Quiz points: {{ $quizMaxPoints }}</div>
            </div>
            <a class="text-indigo-600 hover:underline" href="{{ route('teacher.progress.index') }}">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Student</th>
                                <th class="py-2">Assignments Submitted</th>
                                <th class="py-2">Quiz Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($class->students as $student)
                                @php
                                    $submitted = (int) ($submissionsByStudent[$student->id] ?? 0);
                                    $quizPts = (int) ($quizPointsByStudent[$student->id] ?? 0);
                                @endphp
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $student->name }}</td>
                                    <td class="py-3">{{ $submitted }} / {{ $assignmentsTotal }}</td>
                                    <td class="py-3">{{ $quizPts }} / {{ $quizMaxPoints }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-gray-500">No students enrolled.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
