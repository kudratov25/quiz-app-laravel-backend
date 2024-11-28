<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuizResource;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QuizPlayController extends Controller
{

    public function getQuiz($id){
        
    }
    public function startQuiz(Request $request, $quizId)
    {
        // Find the quiz and check if it has expired
        $quiz = Quiz::findOrFail($quizId);

        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return response()->json(['message' => 'This quiz has expired and cannot be played anymore.'], 400);
        }
        $quizResult = QuizResult::where('user_id', auth()->user()->id)
            ->where('quiz_id', $quiz->id)
            ->where('is_finished', 0)
            ->get()->first();
        // Create a new quiz result for the user
        if (!$quizResult) {
            $quizResult = QuizResult::create([
                'user_id' => $request->user()->id,
                'quiz_id' => $quiz->id,
                'start_time' => now(),
                'total_questions' => $quiz->questions->count(),
            ]);
        }

        // Paginate the questions (only showing 5 per page)
        $questions = $quiz->questions()->with('options:id,question_id,text,image_url')->paginate(5);

        return response()->json([
            'quiz' => new QuizResource($quiz),
            'quiz_result_id' => $quizResult->id,
            'start_time' => $quizResult->start_time,
            'questions' => QuestionResource::collection($questions),
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'total_pages' => $questions->lastPage(),
                'total_items' => $questions->total(),
            ],
        ]);
    }





    public function submitAnswer(Request $request, $quizResultId, $questionId)
    {
        // check quiz existance
        $quizResult = QuizResult::findOrFail($quizResultId);
        $quiz = $quizResult->quiz;
        // check quiz deadline
        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return response()->json(['message' => 'The quiz has expired. You cannot submit any more answers.'], 400);
        }


        // check quiz timer(is available after starting)
        if ($quiz->time_limit) {
            $quizStartTime = Carbon::parse($quizResult->start_time);
            $quizTime = $quizStartTime->addMinutes($quiz->time_limit);
            if (now()->greaterThan($quizTime)) {
                return response()->json(['message' => 'Your time limit for the quiz has expired.'], 400);
            }
        }

        // check question existance
        $question = Question::findOrFail($questionId);

        $existingAnswer = Answer::where('quiz_result_id', $quizResultId)
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            return response()->json(['message' => 'You have already answered this question.'], 400);
        }

        $isCorrect = $question->options()
            ->whereIn('id', $request->answer_ids)
            ->where('is_correct', true)
            ->exists();
        $answer = Answer::create([
            'quiz_result_id' => $quizResult->id,
            'question_id' => $question->id,
            'answer_ids' => json_encode($request->answer_ids),
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
}
