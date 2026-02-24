<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->title }}</h2>
            <div class="text-sm text-gray-500">{{ $class->name }} • Quiz</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('student.quizzes.submit', [$class, $quiz]) }}" class="space-y-6">
                @csrf

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-l-4 border-indigo-600">
                        <div class="text-sm text-gray-500">{{ $class->name }}</div>
                        <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $quiz->title }}</div>
                        @if($quiz->description)
                            <div class="text-gray-700 mt-3">{{ $quiz->description }}</div>
                        @endif
                        <div class="text-sm text-gray-500 mt-3">Answer all questions then click Submit.</div>
                    </div>
                </div>

                @foreach($quiz->questions as $idx => $q)
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">Question {{ $idx + 1 }} • {{ $q->points }} pt</div>
                                    <div class="font-semibold text-gray-900 mt-1">{{ $q->question_text }}</div>
                                </div>
                                <div class="text-xs text-gray-400 whitespace-nowrap">{{ $q->type }}</div>
                            </div>

                            <div class="mt-4">
                                @if($q->type === 'MCQ')
                                    <div class="space-y-2">
                                        @foreach(($q->options ?? []) as $opt)
                                            <label class="flex items-center gap-3">
                                                <input class="text-indigo-600 focus:ring-indigo-500" type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" />
                                                <span>{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($q->type === 'TRUE_FALSE')
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-3"><input class="text-indigo-600 focus:ring-indigo-500" type="radio" name="answers[{{ $q->id }}]" value="TRUE" /> <span>True</span></label>
                                        <label class="flex items-center gap-3"><input class="text-indigo-600 focus:ring-indigo-500" type="radio" name="answers[{{ $q->id }}]" value="FALSE" /> <span>False</span></label>
                                    </div>
                                @elseif($q->type === 'IDENTIFICATION')
                                    <input name="answers[{{ $q->id }}]" type="text" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type your answer..." />
                                @elseif($q->type === 'MATCHING')
                                    @php
                                        $pairs = collect($q->options ?? [])->values();
                                        $rights = $pairs->pluck('right')->filter()->values();
                                    @endphp
                                    <div class="space-y-3">
                                        @foreach($pairs as $pIdx => $p)
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                                                <div class="text-gray-800">{{ $p['left'] ?? '' }}</div>
                                                <select name="answers[{{ $q->id }}][{{ $pIdx }}]" class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">Select match</option>
                                                    @foreach($rights as $r)
                                                        <option value="{{ $r }}">{{ $r }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <textarea name="answers[{{ $q->id }}]" rows="3" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Your answer..."></textarea>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <x-input-error :messages="$errors->get('answers')" class="mt-2" />

                <div class="flex justify-end">
                    <x-primary-button>Submit</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
