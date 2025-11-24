<?php

namespace Acme\LivewireQuiz\Http\Livewire\Frontend;

use Acme\LivewireQuiz\Models\Attempt;
use Acme\LivewireQuiz\Models\AttemptAnswer;
use Acme\LivewireQuiz\Models\Quiz;
use Livewire\Component;

class QuizTake extends Component
{
    public Quiz $quiz;
    public $currentIndex = 0;
    public $selectedOption = null;
    public $answers = [];
    public $score = 0;
    public $completed = false;

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load('questions.options');
    }

    public function getCurrentQuestionProperty()
    {
        return $this->quiz->questions->sortBy('order')->values()->get($this->currentIndex);
    }

    public function selectOption($optionId)
    {
        $this->selectedOption = $optionId;
    }

    public function next()
    {
        if (! $this->currentQuestion) {
            return;
        }

        $question = $this->currentQuestion;

        if (! $this->selectedOption) {
            $this->addError('selectedOption', 'Please choose an option.');
            return;
        }

        $this->answers[$question->id] = $this->selectedOption;
        $this->selectedOption = null;

        if ($this->currentIndex + 1 >= $this->quiz->questions->count()) {
            $this->finish();
        } else {
            $this->currentIndex++;
        }
    }

    protected function finish()
    {
        $questions = $this->quiz->questions()->with('options')->get();
        $score = 0;
        $maxScore = 0;

        $attempt = Attempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->quiz->id,
            'score' => 0,
            'max_score' => 0,
            'started_at' => now(),
            'completed_at' => now(),
            'meta' => [
                'answers' => $this->answers,
            ],
        ]);

        foreach ($questions as $question) {
            $maxScore += $question->points ?? 1;
            $selectedOptionId = $this->answers[$question->id] ?? null;
            $selectedOption = $question->options->firstWhere('id', $selectedOptionId);
            $isCorrect = $selectedOption && $selectedOption->is_correct;

            if ($isCorrect) {
                $score += $question->points ?? 1;
            }

            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
            ]);
        }

        $attempt->update([
            'score' => $score,
            'max_score' => $maxScore,
        ]);

        $this->score = $score;
        $this->completed = true;
    }

    public function render()
    {
        return view('livewire-quiz::frontend.quiz-take');
    }
}
