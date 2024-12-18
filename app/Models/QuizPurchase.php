<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizPurchase extends Model
{
    use HasFactory;
    protected $fillable = ['quiz_id', 'user_id', 'amount'];
}
