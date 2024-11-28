<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $casts = [
        'answer_ids' => 'array',
    ];

    protected $fillable = [
        'quiz_result_id',
        'question_id',
        'answer_ids',
        'is_correct'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function quizResult()
    {
        return $this->belongsTo(QuizResult::class);
    }
}
