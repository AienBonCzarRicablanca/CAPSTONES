<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
    </x-slot>

    <div class="py-6" x-data="{
        showNewMessage: false,
        searchQuery: '',
        activeTab: 'all',
        
        get filteredConversations() {
            let convos = {{ Js::from($conversations) }};
            if (this.activeTab === 'direct') {
                return convos.filter(c => c.type === 'direct');
            } else if (this.activeTab === 'classes') {
                return convos.filter(c => c.type === 'class');
            }
            return convos;
        },
        
        get filteredUsers() {
            const users = {{ Js::from($allUsers) }};
            if (!this.searchQuery) return users;
            const query = this.searchQuery.toLowerCase();
            return users.filter(u => u.name.toLowerCase().includes(query) || u.email.toLowerCase().includes(query));
        },
        
        getInitial(name) {
            return name ? name.charAt(0).toUpperCase() : '?';
        },
        
        getRoleColor(role) {
            if (role?.name === 'TEACHER') return 'bg-purple-100 text-purple-800';
            if (role?.name === 'STUDENT') return 'bg-blue-100 text-blue-800';
            if (role?.name === 'ADMIN') return 'bg-red-100 text-red-800';
            return 'bg-gray-100 text-gray-800';
        },
        
        getRoleLabel(role) {
            const name = role?.name || 'USER';
            return name.charAt(0) + name.slice(1).toLowerCase();
        },
        
        formatTime(datetime) {
            if (!datetime) return '';
            const date = new Date(datetime);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return diffMins + 'm ago';
            if (diffHours < 24) return diffHours + 'h ago';
            if (diffDays < 7) return diffDays + 'd ago';
            return date.toLocaleDateString();
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden" style="height: calc(100vh - 180px); min-height: 500px;">
                <div class="flex h-full">
                    <!-- Conversations List Sidebar -->
                    <div class="w-full md:w-96 border-r border-gray-200 flex flex-col">
                        <!-- Header with New Message button -->
                        <div class="p-4 border-b border-gray-200">
                            <button @click="showNewMessage = true" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                New Message
                            </button>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="border-b border-gray-200 bg-gray-50">
                            <nav class="flex">
                                <button @click="activeTab = 'all'" 
                                    :class="activeTab === 'all' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800'"
                                    class="flex-1 py-3 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                    All
                                </button>
                                <button @click="activeTab = 'direct'" 
                                    :class="activeTab === 'direct' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800'"
                                    class="flex-1 py-3 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                    Direct
                                </button>
                                <button @click="activeTab = 'classes'" 
                                    :class="activeTab === 'classes' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800'"
                                    class="flex-1 py-3 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                    Classes
                                </button>
                            </nav>
                        </div>

                        <!-- Conversations List -->
                        <div class="flex-1 overflow-y-auto">
                            <template x-if="filteredConversations.length === 0">
                                <div class="p-8 text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <h3 class="mt-4 text-sm font-medium text-gray-900">No conversations yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">Start a new message to begin chatting!</p>
                                </div>
                            </template>

                            <template x-if="filteredConversations.length > 0">
                                <div class="divide-y divide-gray-100">
                                    <template x-for="convo in filteredConversations" :key="convo.type + '-' + convo.id">
                                        <a :href="convo.url" class="block hover:bg-gray-50 transition-colors">
                                            <div class="p-4 flex items-start gap-3">
                                                <!-- Avatar/Icon -->
                                                <div class="flex-shrink-0">
                                                    <template x-if="convo.type === 'direct'">
                                                        <template x-if="convo.photo">
                                                            <img class="h-12 w-12 rounded-full object-cover ring-2 ring-white" 
                                                                :src="`/storage/${convo.photo}`" 
                                                                :alt="convo.name" />
                                                        </template>
                                                    </template>
                                                    <template x-if="convo.type === 'direct'">
                                                        <template x-if="!convo.photo">
                                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-white">
                                                                <span class="text-white font-semibold text-lg" x-text="getInitial(convo.name)"></span>
                                                            </div>
                                                        </template>
                                                    </template>
                                                    <template x-if="convo.type === 'class'">
                                                        <template x-if="convo.photo">
                                                            <img class="h-12 w-12 rounded-lg object-cover ring-2 ring-white" 
                                                                :src="`/storage/${convo.photo}`" 
                                                                :alt="convo.name" />
                                                        </template>
                                                    </template>
                                                    <template x-if="convo.type === 'class'">
                                                        <template x-if="!convo.photo">
                                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center ring-2 ring-white">
                                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                </svg>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-baseline justify-between gap-2">
                                                        <h3 class="text-sm font-semibold text-gray-900 truncate" x-text="convo.name"></h3>
                                                        <span class="text-xs text-gray-500 flex-shrink-0" x-text="formatTime(convo.last_message_at)"></span>
                                                    </div>
                                                    <template x-if="convo.type === 'class' && convo.teacher_name">
                                                        <p class="text-xs text-gray-500" x-text="convo.teacher_name"></p>
                                                    </template>
                                                    <template x-if="convo.type === 'class' && convo.members_count">
                                                        <p class="text-xs text-gray-500" x-text="convo.members_count + ' members'"></p>
                                                    </template>
                                                    <p class="mt-1 text-sm text-gray-600 truncate" x-text="convo.last_message || 'No messages yet'"></p>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Empty State / Instructions -->
                    <div class="hidden md:flex flex-1 items-center justify-center bg-gray-50 p-8">
                        <div class="text-center max-w-md">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 mb-4">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Your Messages</h3>
                            <p class="text-gray-600 mb-6">
                                Send private messages to teachers and students, or chat with your class members in class channels.
                            </p>
                            <button @click="showNewMessage = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Start a Conversation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Message Modal -->
        <div x-show="showNewMessage" 
             x-cloak
             @click.self="showNewMessage = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.stop 
                 class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] flex flex-col"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">New Message</h3>
                    <button @click="showNewMessage = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Search -->
                <div class="p-4 border-b border-gray-200">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input x-model="searchQuery" 
                               type="text" 
                               placeholder="Search users by name or email..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <!-- Users List -->
                <div class="flex-1 overflow-y-auto p-4">
                    <template x-if="filteredUsers.length === 0">
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No users found</p>
                        </div>
                    </template>

                    <div class="space-y-2">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <a :href="`{{ route('messages.show', '') }}/${user.id}`" 
                               @click="showNewMessage = false"
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <template x-if="user.profile_photo_path">
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white" 
                                         :src="`/storage/${user.profile_photo_path}`" 
                                         :alt="user.name" />
                                </template>
                                <template x-if="!user.profile_photo_path">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-white">
                                        <span class="text-white font-semibold" x-text="getInitial(user.name)"></span>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="user.email"></p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" 
                                      :class="getRoleColor(user.role)" 
                                      x-text="getRoleLabel(user.role)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
