<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quiz Result: {{ $quiz->title }}</h2>
            <div class="text-sm text-gray-500">{{ $class->name }}</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="text-3xl font-semibold">Score: {{ $score }} / {{ $max }}</div>
                    <div class="text-sm text-gray-500 mt-1">Submitted: {{ $submission->submitted_at?->format('M d, Y h:i A') }}</div>
                    <div class="text-sm text-gray-500 mt-1">Short answers may require manual review by teacher.</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="font-semibold">Review</div>

                    @foreach($quiz->questions->sortBy(['order','id']) as $idx => $q)
                        @php
                            $ans = $q->answers->first();
                            $raw = $ans?->answer_text;
                            $decoded = null;
                            if ($q->type === 'MATCHING' && $raw) {
                                $decoded = json_decode((string) $raw, true);
                            }
                        @endphp

                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500">Question {{ $idx + 1 }} • {{ $q->points }} pt • {{ $q->type }}</div>
                            <div class="font-semibold mt-1">{{ $q->question_text }}</div>

                            <div class="mt-3 text-sm">
                                <div class="text-gray-600">Your answer</div>
                                <div class="mt-1">
                                    @if($q->type === 'MATCHING')
                                        @if(is_array($decoded))
                                            <div class="space-y-1">
                                                @foreach($decoded as $k => $v)
                                                    <div>{{ $k }}: {{ $v }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ $raw ?? '—' }}
                                        @endif
                                    @else
                                        {{ $raw ?? '—' }}
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 text-sm">
                                <div class="text-gray-600">Correct answer</div>
                                <div class="mt-1">
                                    @if($q->type === 'MATCHING')
                                        <div class="space-y-1">
                                            @foreach(($q->options ?? []) as $pair)
                                                <div>{{ $pair['left'] ?? '' }}: {{ $pair['right'] ?? '' }}</div>
                                            @endforeach
                                        </div>
                                    @elseif($q->correct_answer)
                                        {{ $q->correct_answer }}
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 text-sm text-gray-600">
                                Awarded: {{ (int) ($ans?->awarded_points ?? 0) }}
                                @if(!is_null($ans?->is_correct))
                                    • {{ $ans->is_correct ? 'Correct' : 'Incorrect' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <a class="text-indigo-600 hover:underline" href="{{ route('student.quizzes.index', $class) }}">Back to quizzes</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
