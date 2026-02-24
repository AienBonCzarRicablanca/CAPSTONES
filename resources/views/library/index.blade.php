<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg">
                <span class="text-4xl">📚</span>
            </div>
            <div>
                <h2 class="font-bold text-3xl text-gray-800">Learning Library</h2>
                <p class="text-gray-600 text-sm">Discover stories and lessons to grow your mind!</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Language Tabs (Sticky at Top) -->
            <div class="sticky top-0 z-40 bg-white shadow-md border-b-4 border-gray-200">
                <div class="max-w-7xl mx-auto">
                    <div class="flex" x-data="{ activeTab: '{{ request('language', 'English') }}' }">
                        <a href="{{ route('library.index', ['language' => 'English', 'difficulty' => request('difficulty'), 'category' => request('category')]) }}" 
                           class="flex-1 py-6 px-8 text-center font-bold text-lg transition-all duration-200 border-b-4"
                           :class="activeTab === 'English' ? 'bg-blue-50 border-blue-600 text-blue-700' : 'bg-white border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                            <div class="flex items-center justify-center gap-3">
                                <span class="text-2xl">🇬🇧</span>
                                <span class="uppercase tracking-wide">English</span>
                            </div>
                        </a>
                        <a href="{{ route('library.index', ['language' => 'Tagalog', 'difficulty' => request('difficulty'), 'category' => request('category')]) }}" 
                           class="flex-1 py-6 px-8 text-center font-bold text-lg transition-all duration-200 border-b-4"
                           :class="activeTab === 'Tagalog' ? 'bg-purple-50 border-purple-600 text-purple-700' : 'bg-white border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                            <div class="flex items-center justify-center gap-3">
                                <span class="text-2xl">🇵🇭</span>
                                <span class="uppercase tracking-wide">Tagalog</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6">
                    <h3 class="font-bold text-white text-xl flex items-center gap-2">
                        <span class="text-2xl">🔍</span> Filter & Search
                    </h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('library.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <input type="hidden" name="language" value="{{ request('language', 'English') }}">
                        
                        <div>
                            <label class="block text-base font-bold text-gray-700 mb-3">
                                Difficulty Level
                            </label>
                            <select name="difficulty" class="block w-full border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-base py-4 px-5 font-medium transition-all">
                                <option value="">All Levels</option>
                                <option value="Beginner" @selected(request('difficulty') === 'Beginner')>🟢 Easy (Beginner)</option>
                                <option value="Intermediate" @selected(request('difficulty') === 'Intermediate')>🟡 Medium (Intermediate)</option>
                                <option value="Advanced" @selected(request('difficulty') === 'Advanced')>🔴 Hard (Advanced)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-base font-bold text-gray-700 mb-3">
                                Category
                            </label>
                            <select name="category" class="block w-full border-2 border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-base py-4 px-5 font-medium transition-all">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected((string)request('category') === (string)$cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-8 text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                                Search →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Learning Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($items as $item)
                    <a href="{{ route('library.show', $item) }}" 
                       class="group bg-white shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-gray-200">
                        
                        <!-- Card Header -->
                        <div class="relative h-48 bg-gradient-to-br 
                            {{ $item->difficulty === 'Beginner' ? 'from-green-400 to-emerald-500' : '' }}
                            {{ $item->difficulty === 'Intermediate' ? 'from-orange-400 to-yellow-500' : '' }}
                            {{ $item->difficulty === 'Advanced' ? 'from-purple-500 to-pink-600' : '' }}
                            p-6 flex flex-col items-center justify-center">
                            
                            <!-- Category Icon -->
                            <div class="text-7xl mb-3 transform group-hover:scale-110 transition-transform">
                                @if(str_contains($item->category?->name ?? '', 'Alphabet'))
                                    🔤
                                @elseif(str_contains($item->category?->name ?? '', 'Number'))
                                    🔢
                                @elseif(str_contains($item->category?->name ?? '', 'Reading'))
                                    📖
                                @elseif(str_contains($item->category?->name ?? '', 'Grammar'))
                                    ✍️
                                @elseif(str_contains($item->category?->name ?? '', 'Science'))
                                    🔬
                                @elseif(str_contains($item->category?->name ?? '', 'Filipino'))
                                    🇵🇭
                                @elseif(str_contains($item->category?->name ?? '', 'Life'))
                                    🌟
                                @else
                                    📚
                                @endif
                            </div>
                            
                            <!-- Difficulty Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="inline-block bg-white/95 backdrop-blur-sm text-gray-800 font-bold px-4 py-2 text-sm shadow-lg">
                                    @if($item->difficulty === 'Beginner')
                                        🟢 Easy
                                    @elseif($item->difficulty === 'Intermediate')
                                        🟡 Medium
                                    @else
                                        🔴 Hard
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6">
                            <h3 class="font-bold text-xl text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $item->title }}
                            </h3>
                            
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 font-semibold text-sm">
                                    {{ $item->language === 'English' ? '🇬🇧' : '🇵🇭' }} {{ $item->language }}
                                </span>
                                <span class="text-gray-500 text-sm font-medium">
                                    {{ $item->category?->name ?? 'General' }}
                                </span>
                            </div>
                            
                            <!-- Read Button -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <span class="inline-flex items-center text-blue-600 font-bold group-hover:text-purple-600 transition-colors">
                                    Start Reading 
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full">
                        <div class="bg-gray-50 p-16 text-center border border-gray-200">
                            <div class="text-9xl mb-6">📚</div>
                            <h3 class="text-3xl font-bold text-gray-700 mb-3">No stories found</h3>
                            <p class="text-gray-500 text-lg">Try adjusting your filters to discover more content!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($items->hasPages())
                <div class="flex justify-center">
                    <div class="bg-white shadow-lg p-4 border border-gray-200">
                        {{ $items->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
