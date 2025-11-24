<?php

namespace Acme\LivewireQuiz\Http\Livewire\Admin;

use Acme\LivewireQuiz\Models\Option;
use Acme\LivewireQuiz\Models\Question;
use Acme\LivewireQuiz\Models\Quiz;
use Illuminate\Support\Str;
use Livewire\Component;

class QuizManager extends Component
{
    public $quizzes;
    public $quizId;
    public $title = '';
    public $description = '';
    public $is_active = true;
    public $time_limit_minutes = null;

    public $questions = [];

    public $showForm = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
        'time_limit_minutes' => 'nullable|integer|min:1',
        'questions.*.question_text' => 'required|string',
        'questions.*.points' => 'nullable|integer|min:1',
        'questions.*.options.*.option_text' => 'required|string',
        'questions.*.options.*.is_correct' => 'boolean',
    ];

    public function mount()
    {
        $this->loadQuizzes();
    }

    public function loadQuizzes(): void
    {
        $this->quizzes = Quiz::with('questions.options')->orderByDesc('id')->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $quizId): void
    {
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);
        $this->quizId = $quiz->id;
        $this->title = $quiz->title;
        $this->description = $quiz->description;
        $this->is_active = $quiz->is_active;
        $this->time_limit_minutes = $quiz->time_limit_minutes;

        $this->questions = $quiz->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'points' => $question->points,
                'options' => $question->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $this->showForm = true;
    }

    public function addQuestion(): void
    {
        $this->questions[] = [
            'id' => null,
            'question_text' => '',
            'points' => 1,
            'options' => [
                ['id' => null, 'option_text' => '', 'is_correct' => false],
                ['id' => null, 'option_text' => '', 'is_correct' => false],
            ],
        ];
    }

    public function removeQuestion($index): void
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function addOption($qIndex): void
    {
        $this->questions[$qIndex]['options'][] = [
            'id' => null,
            'option_text' => '',
            'is_correct' => false,
        ];
    }

    public function removeOption($qIndex, $oIndex): void
    {
        unset($this->questions[$qIndex]['options'][$oIndex]);
        $this->questions[$qIndex]['options'] = array_values($this->questions[$qIndex]['options']);
    }

    public function save(): void
    {
        $this->validate();

        $quiz = Quiz::updateOrCreate(
            ['id' => $this->quizId],
            [
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'is_active' => (bool) $this->is_active,
                'time_limit_minutes' => $this->time_limit_minutes,
            ]
        );

        $questionIds = [];

        foreach ($this->questions as $order => $questionData) {
            $question = Question::updateOrCreate(
                ['id' => $questionData['id'] ?? null],
                [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                    'type' => 'single_choice',
                    'points' => $questionData['points'] ?? 1,
                    'order' => $order,
                ]
            );
            $questionIds[] = $question->id;

            $optionIds = [];
            foreach ($questionData['options'] as $optionData) {
                $option = Option::updateOrCreate(
                    ['id' => $optionData['id'] ?? null],
                    [
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => (bool) ($optionData['is_correct'] ?? false),
                    ]
                );
                $optionIds[] = $option->id;
            }

            $question->options()->whereNotIn('id', $optionIds)->delete();
        }

        $quiz->questions()->whereNotIn('id', $questionIds)->delete();

        $this->loadQuizzes();
        $this->resetForm();
        $this->showForm = false;

        session()->flash('quiz_message', 'Quiz saved successfully.');
    }

    public function delete(int $quizId): void
    {
        $quiz = Quiz::findOrFail($quizId);
        $quiz->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });
        $quiz->delete();

        $this->loadQuizzes();
        session()->flash('quiz_message', 'Quiz deleted.');
    }

    public function resetForm(): void
    {
        $this->quizId = null;
        $this->title = '';
        $this->description = '';
        $this->is_active = true;
        $this->time_limit_minutes = null;
        $this->questions = [];
    }

    public function render()
    {
        return view('livewire-quiz::admin.quiz-manager');
    }
}
