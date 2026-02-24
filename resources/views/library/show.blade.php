<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('library.index', ['language' => $item->language]) }}" 
                   class="text-2xl hover:scale-110 transition-transform bg-blue-100 hover:bg-blue-200 rounded-full p-2">
                    ↩️
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800">{{ $item->title }}</h2>
                    <div class="flex items-center gap-2 text-sm mt-1">
                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full font-semibold text-xs">
                            {{ $item->language === 'English' ? '🇬🇧' : '🇵🇭' }} {{ $item->language }}
                        </span>
                        <span class="px-3 py-1 rounded-full font-semibold text-xs
                            {{ $item->difficulty === 'Beginner' ? 'bg-green-500 text-white' : '' }}
                            {{ $item->difficulty === 'Intermediate' ? 'bg-orange-500 text-white' : '' }}
                            {{ $item->difficulty === 'Advanced' ? 'bg-purple-600 text-white' : '' }}">
                            @if($item->difficulty === 'Beginner') Easy
                            @elseif($item->difficulty === 'Intermediate') Medium
                            @else Advanced @endif
                        </span>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full font-semibold text-xs">
                            {{ $item->category?->name ?? 'General' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Story Content Card -->
            <div style="background: white; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden;">
                
                <!-- Story Text Section -->
                <div style="padding: 48px;">
                    <div id="story-text" style="font-family: Georgia, serif; color: #374151; line-height: 1.8; font-size: 18px; margin-bottom: 32px;">
                        {!! nl2br(e($item->text_content ?? '')) !!}
                    </div>
                    
                    <!-- Text-to-Speech Controls (Standalone - No Framework Dependency) -->
                    <div style="border-top: 2px solid #e5e7eb; padding-top: 32px; margin-top: 32px;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                            <div style="text-align: center; margin-bottom: 16px;">
                                <h3 style="font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 8px;">🎧 Listen to this story</h3>
                                <p style="color: #6b7280;">Click the button below to hear the story read aloud</p>
                            </div>
                            
                            <button id="tts-button" onclick="toggleSpeech()" 
                                    style="position: relative; padding: 20px 48px; border-radius: 9999px; font-weight: bold; font-size: 20px; transition: all 0.3s; transform: scale(1); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: none; cursor: pointer; background: linear-gradient(to right, #2563eb, #9333ea); color: white;">
                                <span id="btn-text">
                                    <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                                    </svg>
                                    Read Aloud
                                </span>
                            </button>
                            
                            <p id="status-text" style="font-size: 14px; color: #6b7280; margin-top: 8px; display: none;">
                                🎵 Currently reading in {{ $item->language }}...
                            </p>
                        </div>
                    </div>
                    
                    @if($item->audio_path)
                        <div style="margin-top: 32px; padding: 24px; background: linear-gradient(to right, #fdf4ff, #fce7f3); border-radius: 16px; border: 2px solid #e9d5ff;">
                            <div style="display: flex; align-items: center; gap: 8px; color: #7c3aed; font-weight: bold; margin-bottom: 12px;">
                                <svg style="width: 24px; height: 24px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                                </svg>
                                <span>Pre-recorded Audio</span>
                            </div>
                            <audio style="width: 100%;" controls src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->audio_path) }}"></audio>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comprehension Activity -->
            <div style="background: white; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden;">
                <div style="background: linear-gradient(to right, #10b981, #14b8a6); padding: 24px;">
                    <h3 style="font-weight: bold; color: white; font-size: 24px; display: flex; align-items: center; gap: 12px; margin: 0;">
                        <span style="font-size: 32px;">✏️</span> 
                        <span>Comprehension Activity</span>
                    </h3>
                </div>
                
                @if(session('status'))
                    <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 24px;">
                        <p style="color: #065f46; font-weight: bold; font-size: 18px; display: flex; align-items: center; gap: 8px; margin: 0;">
                            <span style="font-size: 32px;">🎉</span> {{ session('status') }}
                        </p>
                    </div>
                @endif
                
                <div style="padding: 32px;">
                    <div style="max-width: 768px; margin: 0 auto;">
                        <form method="POST" action="{{ route('library.activities.store', $item) }}" style="display: flex; flex-direction: column; gap: 24px;">
                            @csrf
                            
                            <div>
                                <label style="display: block; font-weight: bold; color: #1f2937; margin-bottom: 12px; font-size: 18px;">
                                    🎯 Activity Type
                                </label>
                                <select name="type" style="width: 100%; border-radius: 16px; border: 2px solid #d1d5db; font-size: 18px; padding: 16px 20px; font-weight: 500;">
                                    <option value="MCQ">📝 Multiple Choice Questions</option>
                                    <option value="MATCHING">🔗 Matching Activity</option>
                                    <option value="SHORT">💭 Short Answer Questions</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-weight: bold; color: #1f2937; margin-bottom: 12px; font-size: 18px;">
                                    ✍️ Your Answer
                                </label>
                                <textarea name="answers[freeform]" rows="8" 
                                          style="width: 100%; border-radius: 16px; border: 2px solid #d1d5db; font-size: 18px; padding: 16px 20px;" 
                                          placeholder="Share your thoughts about what you learned..."></textarea>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" style="background: linear-gradient(to right, #2563eb, #9333ea); color: white; font-weight: bold; padding: 16px 40px; border-radius: 9999px; font-size: 18px; transition: all 0.3s; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: none; cursor: pointer;">
                                    Submit Answer →
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Cloud Text-to-Speech Script -->
    <script>
        let speaking = false;
        let currentAudio = null;
        
        async function toggleSpeech() {
            const button = document.getElementById('tts-button');
            const btnText = document.getElementById('btn-text');
            const statusText = document.getElementById('status-text');
            
            if (speaking) {
                // Stop speaking
                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio = null;
                }
                speaking = false;
                
                // Reset button
                button.style.background = 'linear-gradient(to right, #2563eb, #9333ea)';
                button.style.animation = 'none';
                btnText.innerHTML = `
                    <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                    </svg>
                    Read Aloud
                `;
                statusText.style.display = 'none';
                return;
            }
            
            // Get text content
            const textElement = document.getElementById('story-text');
            const text = textElement.innerText || textElement.textContent;
            
            if (!text.trim()) {
                alert('No text to read!');
                return;
            }
            
            // Show loading state
            button.disabled = true;
            btnText.innerHTML = `
                <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px; animation: spin 1s linear infinite;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                </svg>
                Loading...
            `;
            statusText.innerText = '⏳ Generating high-quality audio...';
            statusText.style.display = 'block';
            
            const language = '{{ $item->language }}';
            const langCode = language === 'Tagalog' ? 'fil-PH' : 'en-US';
            
            try {
                // Call Laravel API endpoint
                const response = await fetch('{{ route('tts.synthesize') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        text: text,
                        language: langCode
                    })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'Failed to generate speech');
                }
                
                // Convert base64 audio to blob
                const audioData = atob(data.audioContent);
                const audioArray = new Uint8Array(audioData.length);
                for (let i = 0; i < audioData.length; i++) {
                    audioArray[i] = audioData.charCodeAt(i);
                }
                const audioBlob = new Blob([audioArray], { type: 'audio/mp3' });
                const audioUrl = URL.createObjectURL(audioBlob);
                
                // Create and play audio
                currentAudio = new Audio(audioUrl);
                
                currentAudio.onplay = function() {
                    speaking = true;
                    button.disabled = false;
                    button.style.background = 'linear-gradient(to right, #dc2626, #b91c1c)';
                    button.style.animation = 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite';
                    btnText.innerHTML = `
                        <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z"/>
                        </svg>
                        Stop Reading
                    `;
                    statusText.innerHTML = '🔊 Playing with VoiceRSS (' + data.voice + ')';
                };
                
                currentAudio.onended = function() {
                    speaking = false;
                    button.style.background = 'linear-gradient(to right, #2563eb, #9333ea)';
                    button.style.animation = 'none';
                    btnText.innerHTML = `
                        <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                        </svg>
                        Read Aloud
                    `;
                    statusText.style.display = 'none';
                    URL.revokeObjectURL(audioUrl);
                };
                
                currentAudio.onerror = function(e) {
                    speaking = false;
                    button.disabled = false;
                    button.style.background = 'linear-gradient(to right, #2563eb, #9333ea)';
                    button.style.animation = 'none';
                    btnText.innerHTML = `
                        <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                        </svg>
                        Read Aloud
                    `;
                    statusText.style.display = 'none';
                    alert('Error playing audio. Please try again.');
                };
                
                // Play the audio
                await currentAudio.play();
                
            } catch (error) {
                console.error('TTS Error:', error);
                speaking = false;
                button.disabled = false;
                button.style.background = 'linear-gradient(to right, #2563eb, #9333ea)';
                button.style.animation = 'none';
                btnText.innerHTML = `
                    <svg style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                    </svg>
                    Read Aloud
                `;
                statusText.style.display = 'none';
                
                // Show user-friendly error message
                if (error.message.includes('API key not configured')) {
                    alert('⚠️ Text-to-Speech is not yet configured.\n\n' +
                          '📝 Quick Setup (FREE - No Credit Card!):\n' +
                          '1. Go to: https://www.voicerss.org/registration.aspx\n' +
                          '2. Sign up (takes 2 minutes)\n' +
                          '3. Copy your API key\n' +
                          '4. Add to .env file: VOICERSS_API_KEY=your-key\n' +
                          '5. Run: php artisan config:clear\n\n' +
                          'See VOICERSS_SETUP.md for detailed instructions.');
                } else if (error.message.includes('Daily limit reached')) {
                    alert('⏰ Daily limit reached (350 requests/day).\n\nPlease try again tomorrow, or contact administrator about upgrading.');
                } else {
                    alert('Error: ' + error.message + '\n\nPlease try again or contact support.');
                }
            }
        }
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
        });
        
        // Add animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>
