<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reading_passage_id',
        'audio_filename',
        'transcription',
        'accuracy_score',
        'words_per_minute',
        'fluency_score',
        'grade',
        'errors',
        'recommendations',
        'duration_seconds',
        'status',
        'teacher_feedback',
        'teacher_score',
        'assessed_at'
    ];

    protected $casts = [
        'errors' => 'array',
        'recommendations' => 'array',
        'accuracy_score' => 'decimal:2',
        'fluency_score' => 'decimal:2',
        'words_per_minute' => 'integer',
        'duration_seconds' => 'integer',
        'teacher_score' => 'integer',
        'assessed_at' => 'datetime'
    ];

    /**
     * Get the user who made this assessment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reading passage
     */
    public function passage(): BelongsTo
    {
        return $this->belongsTo(ReadingPassage::class, 'reading_passage_id');
    }

    /**
     * Scope to get completed assessments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get pending assessments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the audio file URL
     */
    public function getAudioUrlAttribute(): string
    {
        return asset('recordings/' . $this->audio_filename);
    }

    /**
     * Get grade badge color
     */
    public function getGradeBadgeColorAttribute(): string
    {
        return match($this->grade) {
            'Excellent' => 'green',
            'Good' => 'blue',
            'Fair' => 'yellow',
            'Needs Practice' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Check if assessment needs review
     */
    public function needsReview(): bool
    {
        return $this->status === 'completed' && 
               ($this->accuracy_score < 50 || $this->grade === 'Needs Practice');
    }

    /**
     * Mark as processing
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark as completed with results
     */
    public function complete(array $results): void
    {
        $this->update([
            'status' => 'completed',
            'transcription' => $results['transcription'] ?? null,
            'accuracy_score' => $results['accuracy'] ?? null,
            'words_per_minute' => $results['wpm'] ?? null,
            'fluency_score' => $results['fluency_score'] ?? null,
            'grade' => $results['grade'] ?? null,
            'errors' => $results['errors'] ?? [],
            'recommendations' => $results['recommendations'] ?? [],
            'assessed_at' => now()
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
