@php
    $user = auth()->user();
    $isAdmin = $user?->isAdmin();
    $isTeacher = $user?->isTeacher();
    $isStudent = $user?->isStudent();
@endphp

<div x-data="{ 
        sidebarOpen: window.innerWidth >= 768,
        userManagementOpen: {{ request()->routeIs('admin.teachers.*') || request()->routeIs('admin.students.*') ? 'true' : 'false' }},
        libraryAdminOpen: {{ request()->routeIs('admin.library.*') ? 'true' : 'false' }},
        classesOpen: {{ request()->routeIs('*.classes.*') || request()->routeIs('*.lessons.*') || request()->routeIs('*.assignments.*') || request()->routeIs('*.quizzes.*') ? 'true' : 'false' }}
     }" 
     @resize.window="sidebarOpen = window.innerWidth >= 768"
     class="flex h-screen bg-gray-100">
    
    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-sky-600 to-sky-700 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col"
    >
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-sky-500">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-white">LMS</span>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-white hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- User Profile Section -->
        <div class="px-4 py-4 border-b border-sky-500">
            <div class="flex items-center space-x-3">
                @if($user->profile_photo_path)
                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-sky-400" src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile" />
                @else
                    <div class="h-10 w-10 rounded-full bg-sky-500 flex items-center justify-center ring-2 ring-sky-400">
                        <span class="text-white font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                    <p class="text-xs text-white truncate">
                        @if($isAdmin) Administrator
                        @elseif($isTeacher) Teacher
                        @elseif($isStudent) Student
                        @endif
                    </p>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="text-white hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-sky-600 rounded-md shadow-lg py-1 z-10">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-white hover:bg-sky-500">Profile Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-sky-500">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="px-4 py-3">
            <a href="{{ route('search.index') }}" class="flex items-center w-full px-3 py-2 text-sm text-white bg-sky-600 rounded-lg hover:bg-sky-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Search...</span>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            @if($isAdmin)
                <!-- Admin Menu -->
                
                <!-- User Management -->
                <div>
                    <button @click="userManagementOpen = !userManagementOpen" class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-white rounded-lg hover:bg-sky-600 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>User Management</span>
                        </div>
                        <svg :class="userManagementOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="userManagementOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.teachers.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.teachers.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">Teachers</a>
                        <a href="{{ route('admin.students.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">Students</a>
                    </div>
                </div>

                <!-- Library Admin -->
                <div>
                    <button @click="libraryAdminOpen = !libraryAdminOpen" class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-white rounded-lg hover:bg-sky-600 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            <span>Library</span>
                        </div>
                        <svg :class="libraryAdminOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="libraryAdminOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.library.categories.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.library.categories.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">Categories</a>
                        <a href="{{ route('admin.library.items.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.library.items.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">Items</a>
                    </div>
                </div>

                <!-- Teacher Applications -->
                <a href="{{ route('admin.teacherApplications.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.teacherApplications.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Teacher Applications</span>
                </a>

                <!-- Activity Logs -->
                <a href="{{ route('admin.logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.logs.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Activity Logs</span>
                </a>
            @endif

            @if($isTeacher)
                <!-- Teacher Menu -->
                
                <!-- My Classes -->
                <a href="{{ route('teacher.classes.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.classes.index') || request()->routeIs('teacher.classes.create') || request()->routeIs('teacher.classes.edit') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>My Classes</span>
                </a>

                <!-- Progress Reports -->
                <a href="{{ route('teacher.progress.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.progress.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Progress Reports</span>
                </a>

                <!-- Reading Passages -->
                <a href="{{ route('teacher.passages.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.passages.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Reading Passages</span>
                </a>
            @endif

            @if($isStudent)
                <!-- Student Menu -->
                
                <!-- To Do -->
                <a href="{{ route('student.todo.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('student.todo.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>To Do</span>
                </a>

                <!-- My Classes -->
                <a href="{{ route('student.classes.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('student.classes.index') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>My Classes</span>
                </a>

                <!-- Join Class -->
                <a href="{{ route('student.classes.join') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('student.classes.join') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Join Class</span>
                </a>

                <!-- Progress Reports -->
                <a href="{{ route('student.progress.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('student.progress.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Progress Reports</span>
                </a>

                <!-- Reading Assessment -->
                <a href="{{ route('reading-assessment.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reading-assessment.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <span>Reading Assessment</span>
                </a>
            @endif

            <!-- Library (All Users) -->
            <a href="{{ route('library.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('library.index') || request()->routeIs('library.show') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Library</span>
            </a>

            <!-- Messages -->
            <a href="{{ route('messages.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('messages.*') ? 'bg-sky-500 text-white' : 'text-white hover:bg-sky-600 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span>Messages</span>
            </a>

        </nav>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-sky-500">
            <p class="text-xs text-white text-center">&copy; {{ date('Y') }} LMS. All rights reserved.</p>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Mobile Header -->
        <header class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="text-lg font-semibold text-gray-900">LMS</span>
            <div class="w-6"></div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            
            {{ $slot }}
        </main>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div 
        x-show="sidebarOpen && window.innerWidth < 768" 
        @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
        x-cloak
    ></div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
