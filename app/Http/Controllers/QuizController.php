<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuizResource;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function playQuiz(Request $request, $quizId)
    {
        // Find the quiz and check if it has expired
        $quiz = Quiz::findOrFail($quizId);

        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return response()->json(['message' => 'This quiz has expired and cannot be played anymore.'], 400);
        }

        // Create a new quiz result for the user
        $quizResult = QuizResult::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'total_questions' => $quiz->questions->count(),
        ]);

        // Paginate the questions (only showing 5 per page)
        $questions = $quiz->questions()->with('options:id,question_id,text,image_url')->paginate(5);

        return response()->json([
            'quiz' => new QuizResource($quiz),
            'questions' => QuestionResource::collection($questions),
            'quiz_result_id' => $quizResult->id,
            'start_time' => $quizResult->start_time,
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'total_pages' => $questions->lastPage(),
                'total_items' => $questions->total(),
            ],
        ]);
    }

    public function submitAnswer(Request $request, $quizResultId, $questionId)
    {
        $quizResult = QuizResult::findOrFail($quizResultId);
        $quiz = $quizResult->quiz;
        $question = Question::findOrFail($questionId);

        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return response()->json(['message' => 'The quiz has expired. You cannot submit any more answers.'], 400);
        }

        $quizStartTime = Carbon::parse($quizResult->start_time);
        $quizEndTime = $quizStartTime->addMinutes($quiz->time_limit);

        if (now()->greaterThan($quizEndTime)) {
            return response()->json(['message' => 'Your time limit for the quiz has expired.'], 400);
        }

        $existingAnswer = Answer::where('quiz_result_id', $quizResult->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            return response()->json(['message' => 'You have already answered this question.'], 400);
        }

        $isCorrect = $question->options()->whereIn('id', $request->answer_ids)->where('is_correct', true)->exists();

        $answer = Answer::create([
            'quiz_result_id' => $quizResult->id,
            'question_id' => $question->id,
            'answer_id' => $request->answer_ids,
            'is_correct' => $isCorrect,
        ]);

        $score = Answer::where('quiz_result_id', $quizResult->id)
            ->where('is_correct', true)
            ->count();

        $quizResult->score = $score;
        $quizResult->save();

        return response()->json([
            'message' => 'Answer submitted successfully!',
            'score' => $score,
        ]);
    }


    public function togglePublicStatus($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $quiz->is_public = !$quiz->is_public;
        $quiz->save();

        return response()->json(['message' => 'Quiz visibility updated!', 'is_public' => $quiz->is_public]);
    }
}
