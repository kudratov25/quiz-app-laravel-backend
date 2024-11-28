<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $quizzes = Quiz::all();
        return response()->json($quizzes);
    }
}
