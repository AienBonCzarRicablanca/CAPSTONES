<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $assignment->title }}</h2>
                <div class="text-sm text-gray-500">{{ $class->name }} • {{ $assignment->type }}</div>
            </div>
            <div class="flex items-center gap-4">
                <form method="POST" action="{{ route('teacher.assignments.destroy', [$class, $assignment]) }}" onsubmit="return confirm('Are you sure you want to delete this assignment? All submissions will be deleted.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                </form>
                <a class="text-indigo-600 hover:underline" href="{{ route('teacher.assignments.index', $class) }}">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="prose max-w-none">{!! nl2br(e($assignment->description ?? '')) !!}</div>

                    <div class="mt-4 text-sm text-gray-600">
                        Due: <span class="font-medium">{{ $assignment->due_at?->format('M d, Y h:i A') ?? '—' }}</span>
                        • Late turn in: <span class="font-medium">{{ $assignment->allow_late ? 'Allowed' : 'Not allowed' }}</span>
                    </div>

                    @if($assignment->attachment_path)
                        <div class="mt-4">
                            <div class="text-sm text-gray-600">Attachment</div>
                            <a class="text-indigo-600 hover:underline" href="{{ route('assignments.attachment', [$class, $assignment]) }}" target="_blank" rel="noopener">
                                {{ $assignment->attachment_original_name ?? 'View attachment' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <h3 class="font-semibold">Submissions</h3>
                    <table class="min-w-full text-sm mt-4">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Student</th>
                                <th class="py-2">Submitted</th>
                                <th class="py-2">Grade</th>
                                <th class="py-2">Feedback</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignment->submissions as $s)
                                <tr class="border-t align-top">
                                    <td class="py-3">{{ $s->student?->name ?? 'Student' }}</td>
                                    <td class="py-3 text-gray-600">
                                        {{ $s->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                                        @if($s->is_late)
                                            <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Late</span>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ $s->grade ?? '—' }}</td>
                                    <td class="py-3 text-gray-600">{{ $s->feedback ?? '—' }}</td>
                                    <td class="py-3">
                                        @if($s->attachment_path)
                                            <div class="text-sm mb-2">
                                                <a class="text-indigo-600 hover:underline" href="{{ route('submissions.attachment', [$class, $assignment, $s]) }}" target="_blank" rel="noopener">Open attachment</a>
                                            </div>
                                        @endif
                                        <form method="POST" action="{{ route('teacher.submissions.grade', [$class, $assignment, $s]) }}" class="space-y-2">
                                            @csrf
                                            @method('PUT')
                                            <input name="grade" placeholder="0-100" class="w-24 rounded-md border-gray-300" value="{{ old('grade', $s->grade) }}" />
                                            <input name="feedback" placeholder="Feedback" class="w-72 rounded-md border-gray-300" value="{{ old('feedback', $s->feedback) }}" />
                                            <button class="text-indigo-600 hover:underline" type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-gray-500">No submissions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
