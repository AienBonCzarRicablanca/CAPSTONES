<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $assignment->title }}</h2>
                <div class="text-sm text-gray-500">{{ $class->name }} • {{ $assignment->type }}</div>
            </div>
            <a class="text-indigo-600 hover:underline" href="{{ route('student.assignments.index', $class) }}">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                <div class="p-6">
                    <h3 class="font-semibold">Your Submission</h3>
                    <div class="text-sm text-gray-600 mt-1">Grade: {{ $submission?->grade ?? '—' }} • Feedback: {{ $submission?->feedback ?? '—' }}</div>

                    @if($errors->any())
                        <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($submission?->submitted_at)
                        <div class="mt-4 text-sm text-gray-700">
                            Status: <span class="font-medium">Submitted</span>
                            <span class="text-gray-500">({{ $submission->submitted_at->format('M d, Y h:i A') }})</span>
                            @if($submission->is_late)
                                <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Late</span>
                            @endif
                        </div>

                        @if($submission?->content)
                            <div class="mt-4">
                                <div class="text-sm text-gray-600">Answer / Notes</div>
                                <div class="mt-1 whitespace-pre-wrap rounded-md border border-gray-200 p-3 text-sm text-gray-800">{{ $submission->content }}</div>
                            </div>
                        @endif

                        @if($submission?->attachment_path)
                            <div class="mt-4 text-sm">
                                <a class="text-indigo-600 hover:underline" href="{{ route('submissions.attachment', [$class, $assignment, $submission]) }}" target="_blank" rel="noopener">Open attachment</a>
                            </div>
                        @endif

                        @if(!$assignment->due_at || now()->lessThan($assignment->due_at))
                            <form method="POST" action="{{ route('student.submissions.unsubmit', [$class, $assignment]) }}" class="mt-4 flex justify-end">
                                @csrf
                                <x-secondary-button>Undo Submit</x-secondary-button>
                            </form>
                        @else
                            <div class="mt-4 text-sm text-gray-500">Undo is no longer available because the due date has passed.</div>
                        @endif
                    @else
                        @if($assignment->due_at && now()->greaterThan($assignment->due_at))
                            <div class="mt-4 text-sm text-gray-500">Submission is closed because the due date has passed.</div>
                        @else
                            <form method="POST" action="{{ route('student.submissions.store', [$class, $assignment]) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="content" value="Answer / Notes" />
                                    <textarea id="content" name="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $submission?->content) }}</textarea>
                                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="attachment" value="Attachment (optional)" />
                                    <input id="attachment" name="attachment" type="file" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                                    @if($submission?->attachment_path)
                                        <div class="mt-2 text-sm">
                                            <a class="text-indigo-600 hover:underline" href="{{ route('submissions.attachment', [$class, $assignment, $submission]) }}" target="_blank" rel="noopener">Open current attachment</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex justify-end">
                                    <x-primary-button>Submit</x-primary-button>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
