<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::all();
        return response()->json($questions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|string',
            'question_text' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $question = Question::create($request->all());

        return response()->json($question, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|string',
            'question_text' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $question = Question::findOrFail($id);
        $question->update($request->all());

        return response()->json($question);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Question::find($id);
        $question->delete();

        return response()->json(null, 204);
    }
}
