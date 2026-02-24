<x-app-layout>
    <div class="flex flex-col h-screen" x-data="{
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }" x-init="$nextTick(() => scrollToBottom())">
        <!-- Chat Header -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-4 min-w-0">
                        <!-- Back Button -->
                        <a href="{{ route('messages.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors" title="Back to messages">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <!-- User Avatar & Info -->
                        <div class="flex items-center gap-3 min-w-0">
                            @if($otherUser->profile_photo_path)
                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-indigo-100 shadow-sm" 
                                     src="{{ Storage::url($otherUser->profile_photo_path) }}" 
                                     alt="{{ $otherUser->name }}" />
                            @else
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-indigo-100 shadow-sm">
                                    <span class="text-white font-semibold text-lg">{{ substr($otherUser->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h2 class="font-semibold text-lg text-gray-900 truncate">{{ $otherUser->name }}</h2>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($otherUser->role?->name === 'TEACHER') bg-purple-100 text-purple-800
                                        @elseif($otherUser->role?->name === 'STUDENT') bg-blue-100 text-blue-800
                                        @elseif($otherUser->role?->name === 'ADMIN') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(strtolower($otherUser->role?->name ?? 'User')) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('users.show', $otherUser) }}" 
                           class="hidden sm:inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-300 transition-all">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 overflow-hidden bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-4xl mx-auto h-full flex flex-col">
                <div x-ref="messagesContainer" class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
                    @forelse($messages as $m)
                        <div class="flex {{ (int) $m->sender_id === (int) auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="flex gap-2 max-w-[85%] {{ (int) $m->sender_id === (int) auth()->id() ? 'flex-row-reverse' : 'flex-row' }}>
                                <!-- Avatar (for received messages) -->
                                @if((int) $m->sender_id !== (int) auth()->id())
                                    @if($otherUser->profile_photo_path)
                                        <img class="h-8 w-8 rounded-full object-cover flex-shrink-0 mt-1 ring-2 ring-white shadow-sm" 
                                             src="{{ Storage::url($otherUser->profile_photo_path) }}" 
                                             alt="{{ $otherUser->name }}" />
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center flex-shrink-0 mt-1 ring-2 ring-white shadow-sm">
                                            <span class="text-white font-semibold text-sm">{{ substr($otherUser->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                @endif

                                <div class="flex flex-col {{ (int) $m->sender_id === (int) auth()->id() ? 'items-end' : 'items-start' }}">
                                    <!-- Message Content -->
                                    @if($m->body)
                                        <div class="text-sm whitespace-pre-line break-words {{ (int) $m->sender_id === (int) auth()->id() ? 'text-gray-900' : 'text-gray-900' }}">{{ $m->body }}</div>
                                    @endif

                                    @if($m->attachment_path)
                                        @php
                                            $mime = (string) ($m->attachment_mime_type ?? '');
                                            $isImage = str_starts_with($mime, 'image/');
                                            $isVideo = str_starts_with($mime, 'video/');
                                        @endphp

                                        <div class="{{ $m->body ? 'mt-2' : '' }}">
                                            @if($isImage)
                                                <a href="{{ route('messages.attachment', [$otherUser, $m], false) }}" target="_blank" rel="noopener">
                                                    <img class="max-h-64 rounded-lg shadow-sm" 
                                                         src="{{ route('messages.attachment', [$otherUser, $m], false) }}" 
                                                         alt="Image" />
                                                </a>
                                            @elseif($isVideo)
                                                <video class="max-h-64 rounded-lg shadow-sm" 
                                                       controls 
                                                       src="{{ route('messages.attachment', [$otherUser, $m], false) }}"></video>
                                            @else
                                                <a class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-900 transition-colors" 
                                                   href="{{ route('messages.attachment', [$otherUser, $m], false) }}" 
                                                   target="_blank" 
                                                   rel="noopener">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-medium">{{ $m->attachment_original_name ?? 'View file' }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                    <!-- Timestamp -->
                                    <div class="mt-1 px-2 text-xs text-gray-500">
                                        {{ $m->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900">No messages yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Start the conversation by sending a message below.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Message Input -->
                <div class="border-t border-gray-200 bg-white px-4 sm:px-6 lg:px-8 py-4">
                    <form method="POST" 
                          action="{{ route('messages.store', $otherUser) }}" 
                          enctype="multipart/form-data"
                          @submit="$nextTick(() => scrollToBottom())"
                          x-data="{ fileName: '' }"
                          class="space-y-2">
                        @csrf
                        
                        <!-- File name display -->
                        <div x-show="fileName" class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-sm">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span x-text="fileName" class="flex-1 truncate text-indigo-900 font-medium"></span>
                            <button type="button" 
                                    @click="fileName = ''; $refs.fileInput.value = ''" 
                                    class="text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex-1">
                                <textarea name="message" 
                                          rows="1"
                                          placeholder="Type a message..."
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
                                          style="min-height: 42px; max-height: 120px;"
                                          @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- File Attachment Button -->
                            <input x-ref="fileInput"
                                   id="attachment" 
                                   name="attachment" 
                                   type="file" 
                                   class="hidden"
                                   @change="fileName = $event.target.files[0]?.name || ''" />
                            <label for="attachment"
                                   class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 cursor-pointer transition-all"
                                   title="Attach file">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </label>

                            <!-- Send Button -->
                            <button type="submit" 
                                    class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </div>
                        @error('attachment')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
