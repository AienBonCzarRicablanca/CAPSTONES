<x-app-layout>
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <a href="{{ route('reading-assessment.index') }}" class="text-blue-500 hover:text-blue-600 text-sm mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Passages
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Reading Assessment Results</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $assessment->passage->title }} - {{ $assessment->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        @if($assessment->status === 'pending' || $assessment->status === 'processing')
            <!-- Processing State -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-12 text-center">
                    <i class="fas fa-spinner fa-spin text-blue-500 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Processing Your Recording...</h3>
                    <p class="text-gray-600">This may take a few moments. The page will automatically refresh when complete.</p>
                </div>
            </div>

            <script>
                // Poll for results
                setTimeout(() => {
                    location.reload();
                }, 3000);
            </script>
        @elseif($assessment->status === 'failed')
            <!-- Failed State -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-12 text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Assessment Failed</h3>
                    <p class="text-gray-600 mb-4">We couldn't process your recording. Please try again.</p>
                    <a href="{{ route('reading-assessment.show', $assessment->passage) }}" 
                       class="inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Try Again
                    </a>
                </div>
            </div>
        @else
            <!-- Grade Banner -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-8 text-center">
                    <div class="inline-block px-8 py-3 {{ $assessment->grade_badge_color }} rounded-full text-2xl font-bold mb-4">
                        {{ $assessment->grade }}
                    </div>
                    <div class="text-gray-600">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ $assessment->assessed_at->format('F d, Y \a\t h:i A') }}
                    </div>
                </div>
            </div>

            <!-- Score Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Accuracy -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-700">Accuracy</h4>
                        <i class="fas fa-bullseye text-blue-500 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($assessment->accuracy_score, 1) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $assessment->accuracy_score }}%"></div>
                    </div>
                </div>

                <!-- Words Per Minute -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-700">Reading Speed</h4>
                        <i class="fas fa-tachometer-alt text-green-500 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $assessment->words_per_minute }}</div>
                    <div class="text-sm text-gray-600">words per minute</div>
                </div>

                <!-- Fluency -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-700">Fluency</h4>
                        <i class="fas fa-wave-square text-purple-500 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ number_format($assessment->fluency_score, 1) }}/5.0</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($assessment->fluency_score / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Audio Recording -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-headphones mr-2"></i>Your Recording
                    </h3>
                    <audio controls class="w-full" src="{{ $assessment->audio_url }}"></audio>
                    <div class="text-sm text-gray-600 mt-2">
                        Duration: {{ $assessment->duration_seconds }} seconds
                    </div>
                </div>
            </div>

            <!-- Transcription Comparison -->
            @if($assessment->transcription)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-file-alt mr-2"></i>Transcription
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Expected Text:</h4>
                                <div class="p-4 bg-gray-50 rounded-lg text-sm leading-relaxed">
                                    {!! nl2br(e($assessment->passage->content)) !!}
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">What You Read:</h4>
                                <div class="p-4 bg-blue-50 rounded-lg text-sm leading-relaxed">
                                    {!! nl2br(e($assessment->transcription)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Errors -->
            @if(!empty($assessment->errors) && count($assessment->errors) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-exclamation-circle text-orange-500 mr-2"></i>Areas for Improvement
                        </h3>
                        <div class="space-y-3">
                            @foreach($assessment->errors as $error)
                                <div class="flex items-start p-3 bg-orange-50 border-l-4 border-orange-400 rounded">
                                    <i class="fas fa-info-circle text-orange-500 mt-1 mr-3"></i>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800">
                                            {{ ucfirst($error['type'] ?? 'Error') }}
                                        </div>
                                        @if(isset($error['expected']) && isset($error['said']))
                                            <div class="text-sm text-gray-700 mt-1">
                                                Expected: <span class="font-mono bg-white px-2 py-1 rounded">{{ $error['expected'] }}</span>
                                                <br>
                                                You said: <span class="font-mono bg-white px-2 py-1 rounded">{{ $error['said'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recommendations -->
            @if(!empty($assessment->recommendations) && count($assessment->recommendations) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Recommendations
                        </h3>
                        <ul class="space-y-2">
                            @foreach($assessment->recommendations as $recommendation)
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                    <span class="text-gray-700">{{ $recommendation }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Teacher Feedback -->
            @if($assessment->teacher_feedback)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-comment text-blue-500 mr-2"></i>Teacher Feedback
                        </h3>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-gray-700">{{ $assessment->teacher_feedback }}</p>
                            @if($assessment->teacher_score)
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <span class="text-sm text-gray-600">Teacher Score:</span>
                                    <span class="text-lg font-bold text-blue-600 ml-2">{{ $assessment->teacher_score }}/100</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-4">
                <a href="{{ route('reading-assessment.show', $assessment->passage) }}" 
                   class="flex-1 text-center px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-redo mr-2"></i>Try Again
                </a>
                <a href="{{ route('reading-assessment.history') }}" 
                   class="flex-1 text-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-history mr-2"></i>View History
                </a>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
