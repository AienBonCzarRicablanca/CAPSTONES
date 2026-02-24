<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->title }}</h2>
                <div class="text-sm text-gray-500">{{ $class->name }}</div>
            </div>
            <div class="flex items-center gap-4">
                <form method="POST" action="{{ route('teacher.quizzes.destroy', [$class, $quiz]) }}" onsubmit="return confirm('Are you sure you want to delete this quiz? All questions and attempts will be deleted.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                </form>
                <a class="text-indigo-600 hover:underline" href="{{ route('teacher.quizzes.results', [$class, $quiz]) }}">Results</a>
                @if($quiz->is_published)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800">Published</span>
                @else
                    <form method="POST" action="{{ route('teacher.quizzes.publish', [$class, $quiz]) }}" onsubmit="return confirm('Publish this quiz? Students will be able to see and take it.')">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md text-xs uppercase tracking-widest">Done</button>
                    </form>
                @endif
                <a class="text-indigo-600 hover:underline" href="{{ route('teacher.quizzes.index', $class) }}">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="prose max-w-none">{!! nl2br(e($quiz->description ?? '')) !!}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="font-semibold">Add Question</h3>

                    @if($quiz->is_published)
                        <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                            This quiz is published and locked. You can no longer add or delete questions.
                        </div>
                    @else

                    @if($errors->any())
                        <div class="mt-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <div class="font-semibold">Please fix the following:</div>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                @foreach($errors->all() as $msg)
                                    <li>{{ $msg }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('teacher.quizQuestions.store', [$class, $quiz]) }}"
                        class="mt-4 space-y-4"
                        x-data="{
                            type: 'MCQ',
                            options: ['Option 1', 'Option 2'],
                            correct: '',
                            pairs: [{ left: '', right: '' }],
                            init() {
                                this.ensureDefaultCorrect();
                            },
                            ensureDefaultCorrect() {
                                if (this.type !== 'MCQ') return;
                                const first = this.options.find(o => (o ?? '').trim() !== '');
                                if ((this.correct ?? '').trim() === '' && first) this.correct = first;
                            },
                            syncCorrect() {
                                if (this.type !== 'MCQ') return;
                                const exists = this.options.includes(this.correct);
                                if (!exists) this.correct = '';
                                this.ensureDefaultCorrect();
                            },
                            addOption() {
                                this.options.push('');
                                this.syncCorrect();
                            },
                            removeOption(idx) {
                                if (this.options.length <= 2) return;
                                this.options.splice(idx, 1);
                                this.syncCorrect();
                            },
                            addPair() {
                                this.pairs.push({ left: '', right: '' });
                            },
                            removeLastPair() {
                                if (this.pairs.length <= 1) return;
                                this.pairs.pop();
                            },
                        }"
                        x-init="init()"
                    >
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label value="Type" />
                                <select name="type" x-model="type" @change="ensureDefaultCorrect()" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="MCQ">Multiple Choice</option>
                                    <option value="TRUE_FALSE">True / False</option>
                                    <option value="IDENTIFICATION">Identification</option>
                                    <option value="MATCHING">Matching</option>
                                    <option value="SHORT">Short (manual check)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Points" />
                                <x-text-input name="points" type="number" class="mt-1 block w-full" value="1" required />
                            </div>
                            <div>
                                <x-input-label value="Order" />
                                <x-text-input name="order" type="number" class="mt-1 block w-full" value="0" />
                            </div>
                            <div>
                                <x-input-label value="Correct Answer" />

                                <div x-cloak>
                                    <template x-if="type === 'MCQ'">
                                        <div>
                                        <select name="correct_answer" x-model="correct" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select correct option</option>
                                            <template x-for="(opt, idx) in options" :key="idx">
                                                <option :value="options[idx]" x-text="options[idx]"></option>
                                            </template>
                                        </select>

                                        <div class="mt-2 text-xs text-gray-500">Options</div>
                                        <div class="mt-2 space-y-2">
                                            <template x-for="(opt, idx) in options" :key="idx">
                                                <div class="flex items-center gap-2">
                                                    <input
                                                        type="text"
                                                        name="options[]"
                                                        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                        x-model="options[idx]"
                                                        @input="syncCorrect()"
                                                        placeholder="Option"
                                                    />
                                                    <button
                                                        type="button"
                                                        class="text-sm text-red-600 hover:underline"
                                                        @click="removeOption(idx)"
                                                        x-show="options.length > 2"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="text-sm text-indigo-600 hover:underline" @click="addOption()">Add option</button>
                                        </div>
                                        </div>
                                    </template>

                                    <template x-if="type === 'TRUE_FALSE'">
                                        <div>
                                        <select name="correct_answer" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="TRUE">True</option>
                                            <option value="FALSE">False</option>
                                        </select>
                                        </div>
                                    </template>

                                    <template x-if="type === 'IDENTIFICATION'">
                                        <div>
                                        <x-text-input name="correct_answer" class="mt-1 block w-full" placeholder="Correct answer (optional)" />
                                        <div class="mt-2 text-xs text-gray-500">If set, grading is automatic (case-insensitive).</div>
                                        </div>
                                    </template>

                                    <template x-if="type === 'SHORT'">
                                        <div>
                                        <x-text-input disabled class="mt-1 block w-full" value="Manual check" />
                                        </div>
                                    </template>

                                    <template x-if="type === 'MATCHING'">
                                        <div>
                                        <x-text-input disabled class="mt-1 block w-full" value="Auto from pairs" />

                                        <div class="mt-2 text-xs text-gray-500">Pairs</div>
                                        <div class="mt-2 space-y-2">
                                            <template x-for="(p, idx) in pairs" :key="idx">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <input type="text" name="pairs_left[]" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" x-model="pairs[idx].left" placeholder="Left" />
                                                    <input type="text" name="pairs_right[]" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" x-model="pairs[idx].right" placeholder="Right" />
                                                </div>
                                            </template>
                                            <div class="flex items-center gap-3">
                                                <button type="button" class="text-sm text-indigo-600 hover:underline" @click="addPair()">Add pair</button>
                                                <button type="button" class="text-sm text-red-600 hover:underline" x-show="pairs.length > 1" @click="removeLastPair()">Remove last</button>
                                            </div>
                                        </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-input-label value="Question" />
                            <textarea name="question_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        </div>

                        <div class="text-sm text-gray-500">
                            Matching/MCQ options are filled on the right panel.
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Add</x-primary-button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <h3 class="font-semibold">Questions</h3>
                    <table class="min-w-full text-sm mt-4">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">#</th>
                                <th class="py-2">Type</th>
                                <th class="py-2">Question</th>
                                <th class="py-2">Points</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quiz->questions as $qq)
                                <tr class="border-t align-top">
                                    <td class="py-3">{{ $qq->order }}</td>
                                    <td class="py-3 text-gray-600">{{ $qq->type }}</td>
                                    <td class="py-3">{{ $qq->question_text }}</td>
                                    <td class="py-3">{{ $qq->points }}</td>
                                    <td class="py-3">
                                        @if($quiz->is_published)
                                            <span class="text-xs text-gray-500">Locked</span>
                                        @else
                                            <form method="POST" action="{{ route('teacher.quizQuestions.destroy', [$class, $quiz, $qq]) }}" onsubmit="return confirm('Delete question?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline" type="submit">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-gray-500">No questions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
