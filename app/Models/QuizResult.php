<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'start_time'
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
