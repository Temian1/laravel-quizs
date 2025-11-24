<?php

namespace Acme\LivewireQuiz\Http\Livewire\Frontend;

use Acme\LivewireQuiz\Models\Quiz;
use Livewire\Component;

class QuizList extends Component
{
    public $search = '';

    public function render()
    {
        $quizzes = Quiz::query()
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->orderBy('title')
            ->get();

        return view('livewire-quiz::frontend.quiz-list', [
            'quizzes' => $quizzes,
        ]);
    }
}
