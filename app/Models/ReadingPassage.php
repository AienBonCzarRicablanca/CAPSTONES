<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReadingPassage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'language',
        'difficulty',
        'category_id',
        'word_count',
        'expected_wpm',
        'time_limit_seconds',
        'comprehension_questions',
        'is_active',
        'order'
    ];

    protected $casts = [
        'comprehension_questions' => 'array',
        'is_active' => 'boolean',
        'word_count' => 'integer',
        'expected_wpm' => 'integer',
        'time_limit_seconds' => 'integer'
    ];

    /**
     * Get the category that owns the reading passage
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    /**
     * Get all assessments for this passage
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(ReadingAssessment::class);
    }

    /**
     * Scope to get active passages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by language
     */
    public function scopeLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope to filter by difficulty
     */
    public function scopeDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Get estimated reading time in seconds
     */
    public function getEstimatedTimeAttribute(): int
    {
        return ceil(($this->word_count / $this->expected_wpm) * 60);
    }

    /**
     * Calculate actual word count from content
     */
    public static function calculateWordCount(string $content): int
    {
        return str_word_count(strip_tags($content));
    }
}
