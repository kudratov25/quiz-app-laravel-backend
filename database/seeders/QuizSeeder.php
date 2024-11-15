<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            $quiz = Quiz::create([
                'user_id' => 1,
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'is_public' => $faker->boolean,
                'time_limit' => $faker->numberBetween(10, 60),
                'deadline' => now()->addDays(7),
                'price' => $faker->numberBetween(5, 50),
                'category_id' => 1,
            ]);

            foreach (range(1, 3) as $questionIndex) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'type' => $faker->text(10),
                    'question_text' => $faker->sentence,
                ]);

                foreach (range(1, 4) as $answerIndex) {
                    $question->options()->create([
                        'text' => $faker->word,
                        'is_correct' => $answerIndex === 1,
                    ]);
                }
            }
        }
    }
}
