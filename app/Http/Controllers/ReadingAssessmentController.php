<?php

namespace App\Http\Controllers;

use App\Models\ReadingPassage;
use App\Models\ReadingAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ReadingAssessmentController extends Controller
{
    /**
     * Display available reading passages
     */
    public function index(Request $request)
    {
        $query = ReadingPassage::query()
            ->active()
            ->with('category')
            ->orderBy('order')
            ->orderBy('created_at');

        // Filter by language
        if ($request->filled('language')) {
            $query->language($request->language);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->difficulty($request->difficulty);
        }

        $passages = $query->paginate(12);
        $userAssessments = ReadingAssessment::forUser(auth()->id())
            ->pluck('reading_passage_id')
            ->toArray();

        return view('reading-assessment.index', compact('passages', 'userAssessments'));
    }

    /**
     * Show the reading passage and recording interface
     */
    public function show(ReadingPassage $passage)
    {
        $userAssessments = ReadingAssessment::forUser(auth()->id())
            ->where('reading_passage_id', $passage->id)
            ->completed()
            ->latest()
            ->limit(5)
            ->get();

        return view('reading-assessment.show', compact('passage', 'userAssessments'));
    }

    /**
     * Store the recorded audio and start assessment
     */
    public function store(Request $request, ReadingPassage $passage)
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,webm,ogg|max:10240' // 10MB max
        ]);

        try {
            // Store the audio file
            $audioFile = $request->file('audio');
            $filename = Str::uuid() . '_' . auth()->id() . '.' . $audioFile->getClientOriginalExtension();
            
            // Save to public/recordings directory
            $audioFile->move(public_path('recordings'), $filename);

            // Create assessment record
            $assessment = ReadingAssessment::create([
                'user_id' => auth()->id(),
                'reading_passage_id' => $passage->id,
                'audio_filename' => $filename,
                'status' => 'pending',
                'duration_seconds' => $request->input('duration', 0)
            ]);

            // Process assessment (async in production)
            $this->processAssessment($assessment, $passage);

            return response()->json([
                'success' => true,
                'assessment_id' => $assessment->id,
                'message' => 'Recording uploaded successfully! Analyzing your reading...'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload recording: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assessment results
     */
    public function results(ReadingAssessment $assessment)
    {
        // Check if user owns this assessment
        if ($assessment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('reading-assessment.results', compact('assessment'));
    }

    /**
     * Get assessment status (for polling)
     */
    public function status(ReadingAssessment $assessment)
    {
        if ($assessment->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'status' => $assessment->status,
            'assessment' => $assessment->status === 'completed' ? [
                'accuracy' => $assessment->accuracy_score,
                'wpm' => $assessment->words_per_minute,
                'grade' => $assessment->grade,
                'fluency' => $assessment->fluency_score
            ] : null
        ]);
    }

    /**
     * Process the assessment (call ML service or mock)
     */
    private function processAssessment(ReadingAssessment $assessment, ReadingPassage $passage)
    {
        $assessment->markAsProcessing();

        try {
            // Check if ML service is available
            $mlServiceUrl = config('services.ml_assessment.url', 'http://localhost:5000');
            
            // Try to call ML service
            try {
                $response = Http::timeout(30)
                    ->attach(
                        'audio',
                        file_get_contents(public_path('recordings/' . $assessment->audio_filename)),
                        $assessment->audio_filename
                    )
                    ->post($mlServiceUrl . '/assess', [
                        'expected_text' => $passage->content,
                        'language' => strtolower($passage->language)
                    ]);

                if ($response->successful()) {
                    $results = $response->json()['assessment'] ?? [];
                    $assessment->complete($results);
                    return;
                }
            } catch (\Exception $e) {
                \Log::warning('ML service unavailable, using mock assessment', ['error' => $e->getMessage()]);
            }

            // Fallback: Mock assessment for development
            $mockResults = $this->mockAssessment($passage, $assessment);
            $assessment->complete($mockResults);

        } catch (\Exception $e) {
            \Log::error('Assessment processing failed', ['error' => $e->getMessage()]);
            $assessment->markAsFailed();
        }
    }

    /**
     * Mock assessment for development (remove when ML service ready)
     */
    private function mockAssessment(ReadingPassage $passage, ReadingAssessment $assessment): array
    {
        // Simulate processing delay
        sleep(2);

        // Generate random but realistic scores
        $accuracy = rand(65, 98);
        $wpm = rand(50, 120);
        $fluency = rand(25, 50) / 10; // 2.5 to 5.0

        // Determine grade based on scores
        $score = ($accuracy * 0.5) + ($wpm/2 * 0.3) + ($fluency * 20 * 0.2);
        
        $grade = match(true) {
            $score >= 85 => 'Excellent',
            $score >= 70 => 'Good',
            $score >= 50 => 'Fair',
            default => 'Needs Practice'
        };

        // Mock errors
        $errors = [];
        if ($accuracy < 90) {
            $errors[] = [
                'type' => 'substitution',
                'expected' => 'example',
                'said' => 'exampel',
                'position' => rand(1, 10)
            ];
        }

        // Mock recommendations
        $recommendations = [];
        if ($accuracy < 80) {
            $recommendations[] = "Practice pronouncing difficult words";
        }
        if ($wpm < 60) {
            $recommendations[] = "Try to read a bit faster";
        } else if ($wpm > 120) {
            $recommendations[] = "Slow down for better clarity";
        }
        if ($fluency < 3) {
            $recommendations[] = "Practice reading smoothly without long pauses";
        }
        if (empty($recommendations)) {
            $recommendations[] = "Great job! Keep practicing regularly!";
        }

        return [
            'transcription' => $passage->content, // Mock: assume perfect transcription
            'accuracy' => $accuracy,
            'wpm' => $wpm,
            'fluency_score' => $fluency,
            'grade' => $grade,
            'errors' => $errors,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Show student's assessment history
     */
    public function history()
    {
        $assessments = ReadingAssessment::forUser(auth()->id())
            ->with('passage')
            ->completed()
            ->latest()
            ->paginate(20);

        return view('reading-assessment.history', compact('assessments'));
    }

    /**
     * Delete an assessment
     */
    public function destroy(ReadingAssessment $assessment)
    {
        if ($assessment->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete audio file
        $audioPath = public_path('recordings/' . $assessment->audio_filename);
        if (file_exists($audioPath)) {
            unlink($audioPath);
        }

        $assessment->delete();

        return redirect()->back()->with('success', 'Assessment deleted successfully');
    }
}
