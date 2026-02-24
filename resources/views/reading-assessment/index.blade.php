<x-app-layout>
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Reading Assessment</h2>
                        <p class="text-sm text-gray-600 mt-1">Practice your reading skills and get instant feedback</p>
                    </div>
                    <a href="{{ route('reading-assessment.history') }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-history mr-2"></i>My History
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('reading-assessment.index') }}" class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                        <select name="language" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Languages</option>
                            <option value="English" {{ request('language') == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Tagalog" {{ request('language') == 'Tagalog' ? 'selected' : '' }}>Tagalog</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                        <select name="difficulty" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Levels</option>
                            <option value="Beginner" {{ request('difficulty') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ request('difficulty') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Advanced" {{ request('difficulty') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reading Passages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($passages as $passage)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">{{ $passage->title }}</h3>
                                <div class="flex gap-2 mt-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $passage->language == 'English' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $passage->language }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $passage->difficulty == 'Beginner' ? 'bg-green-100 text-green-800' : 
                                           ($passage->difficulty == 'Intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $passage->difficulty }}
                                    </span>
                                </div>
                            </div>
                            @if(in_array($passage->id, $userAssessments))
                                <i class="fas fa-check-circle text-green-500 text-xl" title="You've attempted this"></i>
                            @endif
                        </div>

                        <!-- Stats -->
                        <div class="space-y-2 mb-4 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span><i class="fas fa-file-alt mr-2"></i>Words:</span>
                                <span class="font-semibold">{{ $passage->word_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span><i class="fas fa-clock mr-2"></i>Est. Time:</span>
                                <span class="font-semibold">{{ $passage->estimated_time }}s</span>
                            </div>
                            <div class="flex justify-between">
                                <span><i class="fas fa-tachometer-alt mr-2"></i>Target WPM:</span>
                                <span class="font-semibold">{{ $passage->expected_wpm }}</span>
                            </div>
                        </div>

                        <!-- Content Preview -->
                        <p class="text-sm text-gray-700 mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($passage->content), 100) }}
                        </p>

                        <!-- Action Button -->
                        <a href="{{ route('reading-assessment.show', $passage) }}" 
                           class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            <i class="fas fa-microphone mr-2"></i>Start Reading
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <i class="fas fa-book-open text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No reading passages available</p>
                        <p class="text-gray-400 text-sm mt-2">Check back later or try different filters</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($passages->hasPages())
            <div class="mt-6">
                {{ $passages->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>
