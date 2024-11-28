<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('is_public', true)
            ->orWhere('user_id', auth()->user()->id)
            ->get();

        return response()->json($quizzes);
    }

    public function show($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        return response()->json($quiz);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|text',
            'category_id' => 'nullable|exists:categories,id',
            'is_public' => 'required|boolean',
            'time_limit' => 'nullable|numeric|min:',
            'deadline' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
        ]);

        $quiz = Quiz::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'is_public' => $request->is_public,
            'time_limit' => $request->time_limit,
            'deadline' => $request->deadline ? Carbon::parse($request->deadline) : null,
            'price' => $request->price,
        ]);

        return response()->json($quiz, 201);
    }


    public function togglePublicStatus($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $quiz->is_public = !$quiz->is_public;
        $quiz->save();

        return response()->json(['message' => 'Quiz visibility updated!', 'is_public' => $quiz->is_public]);
    }
}
