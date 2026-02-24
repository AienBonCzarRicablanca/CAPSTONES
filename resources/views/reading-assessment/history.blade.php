<x-app-layout>
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <a href="{{ route('reading-assessment.index') }}" class="text-blue-500 hover:text-blue-600 text-sm mb-2 inline-block">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Passages
                        </a>
                        <h2 class="text-2xl font-bold text-gray-800">Assessment History</h2>
                        <p class="text-sm text-gray-600 mt-1">Track your reading progress over time</p>
                    </div>
                </div>
            </div>
        </div>

        @if($assessments->count() > 0)
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-700">Total Assessments</h4>
                        <i class="fas fa-list text-gray-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-800">{{ $assessments->total() }}</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-700">Avg Accuracy</h4>
                        <i class="fas fa-bullseye text-blue-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($assessments->avg('accuracy_score'), 1) }}%</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-700">Avg WPM</h4>
                        <i class="fas fa-tachometer-alt text-green-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($assessments->avg('words_per_minute'), 0) }}</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-700">Avg Fluency</h4>
                        <i class="fas fa-wave-square text-purple-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($assessments->avg('fluency_score'), 1) }}/5</div>
                </div>
            </div>

            <!-- Assessment List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">All Assessments</h3>
                    
                    <div class="space-y-4">
                        @foreach($assessments as $assessment)
                            <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-gray-800">{{ $assessment->passage->title }}</h4>
                                        <div class="flex gap-2 mt-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $assessment->passage->language == 'English' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $assessment->passage->language }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $assessment->passage->difficulty == 'Beginner' ? 'bg-green-100 text-green-800' : 
                                                   ($assessment->passage->difficulty == 'Intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $assessment->passage->difficulty }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $assessment->grade_badge_color }}">
                                        {{ $assessment->grade }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <div>
                                        <div class="text-sm text-gray-600">Accuracy</div>
                                        <div class="text-xl font-bold text-blue-600">{{ number_format($assessment->accuracy_score, 1) }}%</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">WPM</div>
                                        <div class="text-xl font-bold text-green-600">{{ $assessment->words_per_minute }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Fluency</div>
                                        <div class="text-xl font-bold text-purple-600">{{ number_format($assessment->fluency_score, 1) }}/5</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Duration</div>
                                        <div class="text-xl font-bold text-gray-600">{{ $assessment->duration_seconds }}s</div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        {{ $assessment->assessed_at->format('M d, Y h:i A') }}
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('reading-assessment.results', $assessment) }}" 
                                           class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </a>
                                        <form action="{{ route('reading-assessment.destroy', $assessment) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this assessment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($assessment->teacher_feedback)
                                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                        <div class="flex items-start">
                                            <i class="fas fa-comment text-blue-500 mt-1 mr-3"></i>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800">Teacher Feedback:</div>
                                                <div class="text-sm text-gray-700 mt-1">{{ Str::limit($assessment->teacher_feedback, 100) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($assessments->hasPages())
                        <div class="mt-6">
                            {{ $assessments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Assessments Yet</h3>
                    <p class="text-gray-600 mb-6">Start practicing your reading skills to see your progress here</p>
                    <a href="{{ route('reading-assessment.index') }}" 
                       class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-book-open mr-2"></i>Browse Reading Passages
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
