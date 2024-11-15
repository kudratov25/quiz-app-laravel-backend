<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('is_public', true)
            ->orWhere('user_id', auth()->id())
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

    public function playQuiz(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        // Check if the quiz has expired globally
        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return response()->json(['message' => 'This quiz has expired and cannot be played anymore.'], 400);
        }

        $questions = $quiz->questions;

        // Create a new quiz result for the user
        $quizResult = QuizResult::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'total_questions' => $questions->count(),
        ]);

        return response()->json([
            'quiz' => $quiz,
            'questions' => $questions,
            'quiz_result_id' => $quizResult->id,
        ]);
    }

    public function togglePublicStatus($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $quiz->is_public = !$quiz->is_public;
        $quiz->save();

        return response()->json(['message' => 'Quiz visibility updated!', 'is_public' => $quiz->is_public]);
    }

    public function submitAnswer(Request $request, $quizResultId, $questionId)
    {
        $quizResult = QuizResult::findOrFail($quizResultId);
        $quiz = $quizResult->quiz;
        $question = Question::findOrFail($questionId);

        // Check if the quiz has expired globally
        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return response()->json(['message' => 'The quiz has expired. You cannot submit any more answers.'], 400);
        }

        // Check if the quiz time limit has been reached
        $quizStartTime = $quizResult->start_time;
        $quizEndTime = $quizStartTime->addMinutes($quiz->time_limit);

        if (now()->greaterThan($quizEndTime)) {
            return response()->json(['message' => 'Your time limit for the quiz has expired.'], 400);
        }

        // Handle the answer submission
        $isCorrect = $question->correct_answer_id === $request->answer_id;

        $answer = UserAnswer::create([
            'quiz_result_id' => $quizResult->id,
            'question_id' => $question->id,
            'answer_id' => $request->answer_id,
            'is_correct' => $isCorrect,
        ]);

        // Calculate and update the user's score
        $score = UserAnswer::where('quiz_result_id', $quizResult->id)
            ->where('is_correct', true)
            ->count();

        $quizResult->score = $score;
        $quizResult->save();

        return response()->json([
            'message' => 'Answer submitted successfully!',
            'score' => $score,
        ]);
    }
}
