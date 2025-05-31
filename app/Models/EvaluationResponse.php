<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationResponse extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_evaluation_id',
        'question_id',
        'response_text',
        'rating_value',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating_value' => 'integer',
    ];
    
    /**
     * Get the evaluation that the response belongs to.
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(EventEvaluation::class, 'event_evaluation_id');
    }
    
    /**
     * Get the question that the response belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }
}
