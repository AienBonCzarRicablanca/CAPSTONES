<x-app-layout>
    <x-slot name="header">
        <div x-data="{
                query: '{{ $q }}',
                loading: false,
                searched: {{ $q !== '' ? 'true' : 'false' }},
                activeTab: 'all',
                classes: @js($classes->toArray()),
                users: @js($users->toArray()),
                debounceTimer: null,
                isStudent: {{ auth()->user()?->isStudent() ? 'true' : 'false' }},
                csrfToken: '{{ csrf_token() }}',
                
                async searchLive() {
                    if (this.query.length < 1) {
                        this.classes = [];
                        this.users = [];
                        this.searched = false;
                        window.dispatchEvent(new CustomEvent('search-results', { 
                            detail: { classes: [], users: [], searched: false }
                        }));
                        return;
                    }
                    
                    this.loading = true;
                    this.searched = true;
                    
                    try {
                        const response = await fetch('{{ route('search.api') }}?q=' + encodeURIComponent(this.query));
                        const data = await response.json();
                        this.classes = data.classes || [];
                        this.users = data.users || [];
                        window.dispatchEvent(new CustomEvent('search-results', { 
                            detail: { classes: data.classes || [], users: data.users || [], searched: true }
                        }));
                    } catch (error) {
                        console.error('Search error:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                
                handleInput() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        this.searchLive();
                    }, 300);
                },
                
                getInitial(name) {
                    return name ? name.charAt(0).toUpperCase() : '?';
                },
                
                getRoleColor(role) {
                    if (role === 'TEACHER') return 'bg-purple-100 text-purple-800';
                    if (role === 'STUDENT') return 'bg-blue-100 text-blue-800';
                    return 'bg-gray-100 text-gray-800';
                },
                
                getRoleLabel(role) {
                    return role ? role.charAt(0) + role.slice(1).toLowerCase() : 'User';
                }
            }" class="w-full">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    x-model="query"
                    @input="handleInput()"
                    type="text"
                    placeholder="Search for classes, teachers, or students..."
                    class="block w-full pl-12 pr-4 py-3 text-base border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    autofocus
                />
                <div x-show="loading" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div x-data="{
                activeTab: 'all',
                classes: @js($classes->toArray()),
                users: @js($users->toArray()),
                searched: {{ $q !== '' ? 'true' : 'false' }},
                isStudent: {{ auth()->user()?->isStudent() ? 'true' : 'false' }},
                
                getInitial(name) {
                    return name ? name.charAt(0).toUpperCase() : '?';
                },
                
                getRoleColor(role) {
                    if (role === 'TEACHER') return 'bg-purple-100 text-purple-800';
                    if (role === 'STUDENT') return 'bg-blue-100 text-blue-800';
                    return 'bg-gray-100 text-gray-800';
                },
                
                getRoleLabel(role) {
                    return role ? role.charAt(0) + role.slice(1).toLowerCase() : 'User';
                }
            }"
            @search-results.window="classes = $event.detail.classes; users = $event.detail.users; searched = $event.detail.searched;"
            class="space-y-6">

                <!-- Filter Tabs -->
                <div x-show="searched" class="bg-white shadow-sm rounded-lg border border-gray-100">
                    <div class="border-b border-gray-200">
                        <nav class="flex px-4">
                            <button @click="activeTab = 'all'" 
                                :class="activeTab === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-4 border-b-2 font-medium text-sm transition-colors">
                                All Results
                                <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100" x-text="classes.length + users.length"></span>
                            </button>
                            <button @click="activeTab = 'classes'" 
                                :class="activeTab === 'classes' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-4 border-b-2 font-medium text-sm transition-colors">
                                Classes
                                <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100" x-text="classes.length"></span>
                            </button>
                            <button @click="activeTab = 'users'" 
                                :class="activeTab === 'users' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-4 border-b-2 font-medium text-sm transition-colors">
                                Users
                                <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100" x-text="users.length"></span>
                            </button>
                        </nav>
                    </div>
                </div>

                <div x-show="searched" class="space-y-6">
                    <!-- Classes Section -->
                    <div x-show="activeTab === 'all' || activeTab === 'classes'" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="font-semibold text-lg text-gray-900">Public Classes</h3>
                        </div>

                        <div x-show="classes.length === 0" class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-gray-900">No public classes found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                        </div>

                        <div x-show="classes.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="cls in classes" :key="cls.id">
                                <div class="bg-white shadow-sm hover:shadow-md sm:rounded-lg border border-gray-100 p-5 transition-all duration-200 hover:border-indigo-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 truncate" x-text="cls.name"></h4>
                                            <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span x-text="cls.teacher_name"></span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Public
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div x-show="isStudent" class="mt-4">
                                        <form method="POST" action="{{ route('student.classes.join.store') }}">
                                            @csrf
                                            <input type="hidden" name="join_code" :value="cls.join_code" />
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                                Join Class
                                            </button>
                                        </form>
                                    </div>
                                    <div x-show="!isStudent" class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 text-center">
                                        Students can join this class
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Users Section -->
                    <div x-show="activeTab === 'all' || activeTab === 'users'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="font-semibold text-lg text-gray-900">Users</h3>
                        </div>

                        <div x-show="users.length === 0" class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-gray-900">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                        </div>

                        <div x-show="users.length > 0" class="bg-white shadow-sm sm:rounded-lg border border-gray-100 divide-y divide-gray-100">
                            <template x-for="user in users" :key="user.id">
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <a :href="user.profile_url" class="flex-shrink-0">
                                            <template x-if="user.profile_photo_url">
                                                <img class="h-12 w-12 rounded-full object-cover ring-2 ring-white" :src="user.profile_photo_url" :alt="user.name" />
                                            </template>
                                            <template x-if="!user.profile_photo_url">
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-white">
                                                    <span class="text-white font-semibold text-lg" x-text="getInitial(user.name)"></span>
                                                </div>
                                            </template>
                                        </a>

                                        <div class="flex-1 min-w-0">
                                            <a :href="user.profile_url" class="block">
                                                <p class="text-base font-semibold text-gray-900 hover:text-indigo-600 transition-colors" x-text="user.name"></p>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getRoleColor(user.role)" x-text="getRoleLabel(user.role)"></span>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <a :href="user.profile_url" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Profile
                                            </a>
                                            <a :href="user.messages_url" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                </svg>
                                                Message
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
