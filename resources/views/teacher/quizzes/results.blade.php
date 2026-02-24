<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Results: {{ $quiz->title }}</h2>
                <div class="text-sm text-gray-500">{{ $class->name }} • Max points: {{ $maxPoints }}</div>
            </div>
            <a class="text-indigo-600 hover:underline" href="{{ route('teacher.quizzes.show', [$class, $quiz]) }}">Back to quiz</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @forelse($students as $student)
                @php
                    $studentAnswers = $answersByStudent->get($student->id, collect());
                    $score = (int) ($scoresByStudent[$student->id] ?? 0);
                @endphp

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $student->email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Total</div>
                                <div class="text-2xl font-semibold">{{ $score }} / {{ $maxPoints }}</div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach($questions as $q)
                                @php $ans = $studentAnswers->get($q->id); @endphp
                                <div class="border rounded-lg p-4">
                                    <div class="text-sm text-gray-500">{{ $q->type }} • {{ $q->points }} pt</div>
                                    <div class="font-semibold mt-1">{{ $q->question_text }}</div>

                                    <div class="mt-3 text-sm">
                                        <div class="text-gray-600">Answer</div>
                                        <div class="mt-1">
                                            @if($q->type === 'MATCHING' && $ans?->answer_text)
                                                @php
                                                    $decoded = json_decode((string) $ans->answer_text, true);
                                                @endphp
                                                @if(is_array($decoded))
                                                    <div class="space-y-1">
                                                        @foreach($decoded as $k => $v)
                                                            <div>{{ $k }}: {{ $v }}</div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{ $ans->answer_text }}
                                                @endif
                                            @else
                                                {{ $ans?->answer_text ?? '—' }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                        <div class="text-sm text-gray-600">
                                            @if($q->type === 'SHORT')
                                                Manual review
                                            @else
                                                @if(is_null($ans?->is_correct))
                                                    Not graded
                                                @elseif($ans->is_correct)
                                                    Correct
                                                @else
                                                    Incorrect
                                                @endif
                                            @endif
                                            • Awarded: {{ (int) ($ans?->awarded_points ?? 0) }}
                                        </div>

                                        @if($q->type === 'SHORT' && $ans)
                                            <form method="POST" action="{{ route('teacher.quizAnswers.review', [$class, $quiz, $q, $ans]) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" min="0" max="{{ $q->points }}" name="awarded_points" value="{{ old('awarded_points', $ans->awarded_points ?? 0) }}" class="w-24 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                                <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                                    <input type="checkbox" name="is_correct" value="1" @checked($ans->is_correct === true) />
                                                    <span>Correct</span>
                                                </label>
                                                <button class="inline-flex items-center px-3 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest" type="submit">Save</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 text-gray-500">No students enrolled.</div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
