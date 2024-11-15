<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz_id',
        'type',
        'question_text',
        'image_url'
    ];
    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
