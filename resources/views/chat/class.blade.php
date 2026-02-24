<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            @if($class->photo_path)
                <img class="h-12 w-12 rounded-lg object-cover ring-2 ring-indigo-100 shadow-sm" src="{{ Storage::url($class->photo_path) }}" alt="Class picture" />
            @else
                <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-indigo-100 shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            @endif
            <div>
                <div class="text-sm text-indigo-600 font-medium">Class Chat</div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">{{ $class->name }}</h2>
            </div>
        </div>
    </x-slot>

    <x-class-nav :class="$class" active="chat" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Chat Container -->
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden border border-gray-200">
                <!-- Messages Area -->
                <div class="h-[32rem] overflow-y-auto p-6 space-y-4 bg-gradient-to-b from-gray-50 to-white">
                    @forelse($messages as $msg)
                        @php
                            $isCurrentUser = $msg->sender && (int) $msg->sender_id === (int) auth()->id();
                        @endphp
                        
                        <div class="flex gap-3 {{ $isCurrentUser ? 'flex-row-reverse' : 'flex-row' }}">
                            <!-- User Avatar -->
                            <div class="flex-shrink-0 mt-1">
                                @if($msg->sender)
                                    @if($msg->sender->profile_photo_path)
                                        <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white shadow-sm" 
                                             src="{{ Storage::url($msg->sender->profile_photo_path) }}" 
                                             alt="{{ $msg->sender->name }}" />
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-white shadow-sm">
                                            <span class="text-white font-semibold text-xs">{{ substr($msg->sender->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Message Content -->
                            <div class="flex flex-col max-w-[70%] {{ $isCurrentUser ? 'items-end' : 'items-start' }}">
                                <!-- Sender Name & Time -->
                                <div class="flex items-center gap-2 mb-1 px-1 {{ $isCurrentUser ? 'flex-row-reverse' : 'flex-row' }}">
                                    @if($msg->sender)
                                        <a class="text-xs font-semibold text-gray-700 hover:text-indigo-600 transition-colors" 
                                           href="{{ route('users.show', $msg->sender) }}">
                                            {{ $msg->sender->name }}
                                        </a>
                                    @else
                                        <span class="text-xs font-semibold text-gray-500">Unknown User</span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $msg->created_at->format('h:i A') }}</span>
                                </div>

                                <!-- Message Content -->
                                @if($msg->message)
                                    <div class="text-sm whitespace-pre-line break-words text-gray-900">{{ $msg->message }}</div>
                                @endif

                                @if($msg->attachment_path)
                                    @php
                                        $mime = (string) ($msg->attachment_mime_type ?? '');
                                        $isImage = str_starts_with($mime, 'image/');
                                        $isVideo = str_starts_with($mime, 'video/');
                                    @endphp

                                    <div class="{{ $msg->message ? 'mt-2' : '' }}">
                                        @if($isImage)
                                            <a href="{{ route('classes.chat.attachment', [$class, $msg], false) }}" target="_blank" rel="noopener">
                                                <img class="max-h-64 rounded-lg shadow-sm" 
                                                     src="{{ route('classes.chat.attachment', [$class, $msg], false) }}" 
                                                     alt="Image" />
                                            </a>
                                        @elseif($isVideo)
                                            <video class="max-h-64 rounded-lg shadow-sm" 
                                                   controls 
                                                   src="{{ route('classes.chat.attachment', [$class, $msg], false) }}"></video>
                                        @else
                                            <a class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-900 transition-colors" 
                                               href="{{ route('classes.chat.attachment', [$class, $msg], false) }}" 
                                               target="_blank" 
                                               rel="noopener">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-xs font-medium">{{ $msg->attachment_original_name ?? 'View file' }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
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
                                <p class="mt-1 text-sm text-gray-500">Be the first to start the conversation!</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Message Input -->
                <div class="border-t border-gray-200 bg-white p-4">
                    <form method="POST" action="{{ route('classes.chat.store', $class) }}" enctype="multipart/form-data" x-data="{ fileName: '' }">
                        @csrf
                        
                        <!-- File name display -->
                        <div x-show="fileName" class="mb-3 flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-sm">
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
                                          placeholder="Type your message..." 
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
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
