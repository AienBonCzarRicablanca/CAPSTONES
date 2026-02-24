<x-app-layout>
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <a href="{{ route('reading-assessment.index') }}" class="text-blue-500 hover:text-blue-600 text-sm mb-2 inline-block">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Passages
                        </a>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $passage->title }}</h2>
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
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $passage->word_count }}</div>
                <div class="text-sm text-gray-600">Words</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                <div class="text-2xl font-bold text-green-600">{{ $passage->expected_wpm }}</div>
                <div class="text-sm text-gray-600">Target WPM</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $passage->estimated_time }}s</div>
                <div class="text-sm text-gray-600">Est. Time</div>
            </div>
        </div>

        <!-- Reading Passage -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Read this passage aloud:</h3>
                <div class="text-lg leading-relaxed text-gray-800 p-6 bg-gray-50 rounded-lg" id="passage-content">
                    {!! nl2br(e($passage->content)) !!}
                </div>
            </div>
        </div>

        <!-- Recording Interface -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" id="recording-interface">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Record Your Reading</h3>
                
                <!-- Recording Status -->
                <div class="mb-6">
                    <div id="status-idle" class="flex items-center justify-center p-4 bg-gray-100 rounded-lg">
                        <i class="fas fa-microphone text-gray-400 text-3xl mr-3"></i>
                        <div>
                            <div class="font-semibold text-gray-700">Ready to record</div>
                            <div class="text-sm text-gray-500">Click the button below to start</div>
                        </div>
                    </div>

                    <div id="status-recording" class="hidden flex items-center justify-center p-4 bg-red-100 rounded-lg">
                        <i class="fas fa-circle text-red-500 animate-pulse text-3xl mr-3"></i>
                        <div>
                            <div class="font-semibold text-red-700">Recording...</div>
                            <div class="text-sm text-red-600">Time: <span id="recording-time">00:00</span></div>
                        </div>
                    </div>

                    <div id="status-recorded" class="hidden flex items-center justify-center p-4 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-500 text-3xl mr-3"></i>
                        <div>
                            <div class="font-semibold text-green-700">Recording complete!</div>
                            <div class="text-sm text-green-600">Duration: <span id="duration">0</span> seconds</div>
                        </div>
                    </div>

                    <div id="status-processing" class="hidden flex items-center justify-center p-4 bg-blue-100 rounded-lg">
                        <i class="fas fa-spinner fa-spin text-blue-500 text-3xl mr-3"></i>
                        <div>
                            <div class="font-semibold text-blue-700">Processing your recording...</div>
                            <div class="text-sm text-blue-600">Please wait while we analyze your reading</div>
                        </div>
                    </div>
                </div>

                <!-- Audio Player (hidden until recorded) -->
                <div id="audio-player-container" class="hidden mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview your recording:</label>
                    <audio id="audio-player" controls class="w-full"></audio>
                </div>

                <!-- Control Buttons -->
                <div class="flex gap-4">
                    <button id="btn-record" 
                            class="flex-1 px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold">
                        <i class="fas fa-microphone mr-2"></i>Start Recording
                    </button>

                    <button id="btn-stop" 
                            class="hidden flex-1 px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold">
                        <i class="fas fa-stop mr-2"></i>Stop Recording
                    </button>

                    <button id="btn-reset" 
                            class="hidden px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>

                    <button id="btn-submit" 
                            class="hidden flex-1 px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold">
                        <i class="fas fa-check mr-2"></i>Submit for Assessment
                    </button>
                </div>

                <!-- Error Message -->
                <div id="error-message" class="hidden mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span id="error-text"></span>
                </div>
            </div>
        </div>

        <!-- Previous Attempts -->
        @if($userAssessments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Your Previous Attempts</h3>
                    <div class="space-y-3">
                        @foreach($userAssessments as $assessment)
                            <a href="{{ route('reading-assessment.results', $assessment) }}" 
                               class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold text-gray-800">
                                            {{ $assessment->created_at->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            Accuracy: {{ number_format($assessment->accuracy_score, 1) }}% • 
                                            WPM: {{ $assessment->words_per_minute }} • 
                                            Grade: {{ $assessment->grade }}
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $assessment->grade_badge_color }}">
                                        {{ $assessment->grade }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
let mediaRecorder;
let audioChunks = [];
let startTime;
let timerInterval;
let audioDuration = 0;

const btnRecord = document.getElementById('btn-record');
const btnStop = document.getElementById('btn-stop');
const btnReset = document.getElementById('btn-reset');
const btnSubmit = document.getElementById('btn-submit');

const statusIdle = document.getElementById('status-idle');
const statusRecording = document.getElementById('status-recording');
const statusRecorded = document.getElementById('status-recorded');
const statusProcessing = document.getElementById('status-processing');

const audioPlayerContainer = document.getElementById('audio-player-container');
const audioPlayer = document.getElementById('audio-player');
const errorMessage = document.getElementById('error-message');
const errorText = document.getElementById('error-text');

// Start Recording
btnRecord.addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = (event) => {
            audioChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            const audioUrl = URL.createObjectURL(audioBlob);
            audioPlayer.src = audioUrl;
            audioPlayerContainer.classList.remove('hidden');
            
            // Show recorded status
            showStatus('recorded');
            showButtons(['reset', 'submit']);
        };

        mediaRecorder.start();
        startTime = Date.now();
        startTimer();

        // Update UI
        showStatus('recording');
        showButtons(['stop']);

    } catch (error) {
        showError('Unable to access microphone. Please check your permissions.');
        console.error('Error accessing microphone:', error);
    }
});

// Stop Recording
btnStop.addEventListener('click', () => {
    mediaRecorder.stop();
    mediaRecorder.stream.getTracks().forEach(track => track.stop());
    stopTimer();
    audioDuration = Math.floor((Date.now() - startTime) / 1000);
    document.getElementById('duration').textContent = audioDuration;
});

// Reset
btnReset.addEventListener('click', () => {
    audioChunks = [];
    audioPlayer.src = '';
    audioPlayerContainer.classList.add('hidden');
    audioDuration = 0;
    showStatus('idle');
    showButtons(['record']);
    hideError();
});

// Submit
btnSubmit.addEventListener('click', async () => {
    if (audioChunks.length === 0) {
        showError('No recording found. Please record your reading first.');
        return;
    }

    showStatus('processing');
    showButtons([]);

    try {
        const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
        const formData = new FormData();
        formData.append('audio', audioBlob, 'recording.wav');
        formData.append('duration', audioDuration);
        formData.append('_token', '{{ csrf_token() }}');

        const response = await fetch('{{ route('reading-assessment.store', $passage) }}', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Redirect to results page
            window.location.href = `/reading-assessment/assessments/${data.assessment_id}/results`;
        } else {
            showError(data.message || 'Failed to submit recording. Please try again.');
            showStatus('recorded');
            showButtons(['reset', 'submit']);
        }
    } catch (error) {
        showError('Network error. Please check your connection and try again.');
        showStatus('recorded');
        showButtons(['reset', 'submit']);
        console.error('Submit error:', error);
    }
});

// Helper Functions
function showStatus(status) {
    statusIdle.classList.add('hidden');
    statusRecording.classList.add('hidden');
    statusRecorded.classList.add('hidden');
    statusProcessing.classList.add('hidden');

    switch(status) {
        case 'idle': statusIdle.classList.remove('hidden'); break;
        case 'recording': statusRecording.classList.remove('hidden'); break;
        case 'recorded': statusRecorded.classList.remove('hidden'); break;
        case 'processing': statusProcessing.classList.remove('hidden'); break;
    }
}

function showButtons(buttons) {
    btnRecord.classList.add('hidden');
    btnStop.classList.add('hidden');
    btnReset.classList.add('hidden');
    btnSubmit.classList.add('hidden');

    buttons.forEach(btn => {
        switch(btn) {
            case 'record': btnRecord.classList.remove('hidden'); break;
            case 'stop': btnStop.classList.remove('hidden'); break;
            case 'reset': btnReset.classList.remove('hidden'); break;
            case 'submit': btnSubmit.classList.remove('hidden'); break;
        }
    });
}

function startTimer() {
    timerInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
        const seconds = (elapsed % 60).toString().padStart(2, '0');
        document.getElementById('recording-time').textContent = `${minutes}:${seconds}`;
    }, 1000);
}

function stopTimer() {
    clearInterval(timerInterval);
}

function showError(message) {
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
}

function hideError() {
    errorMessage.classList.add('hidden');
}
</script>
@endpush
</x-app-layout>
