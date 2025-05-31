<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationQuestion extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'question_text',
        'question_type',
        'display_order',
        'is_required',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'display_order' => 'integer',
    ];
    
    /**
     * Get the event that the question belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Get the responses for this question.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'question_id');
    }
    
    /**
     * Determine if the question is a rating type.
     */
    public function isRatingType(): bool
    {
        return $this->question_type === 'rating';
    }
    
    /**
     * Determine if the question is a text type.
     */
    public function isTextType(): bool
    {
        return $this->question_type === 'text';
    }
}
