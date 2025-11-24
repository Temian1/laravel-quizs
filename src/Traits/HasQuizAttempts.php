<?php

namespace Acme\LivewireQuiz\Traits;

use Acme\LivewireQuiz\Models\Attempt;

trait HasQuizAttempts
{
    public function quizAttempts()
    {
        return $this->hasMany(Attempt::class, 'user_id');
    }

    public function quizAverageScore(): ?float
    {
        $max = $this->quizAttempts()->sum('max_score');
        if ($max === 0) {
            return null;
        }

        $score = $this->quizAttempts()->sum('score');

        return round(($score / $max) * 100, 1);
    }
}
