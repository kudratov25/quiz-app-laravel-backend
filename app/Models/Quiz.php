<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'is_public',
        'price',
        'time_limit',
        'deadline'
    ];
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function quizResults()
    {
        return $this->hasMany(QuizResult::class);
    }
}
