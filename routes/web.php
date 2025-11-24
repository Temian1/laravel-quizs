<?php

use Illuminate\Support\Facades\Route;
use Acme\LivewireQuiz\Http\Livewire\Admin\QuizManager;
use Acme\LivewireQuiz\Http\Livewire\Frontend\QuizList;
use Acme\LivewireQuiz\Http\Livewire\Frontend\QuizTake;

Route::group([
    'prefix' => config('livewire-quiz.route_prefix', 'quiz'),
    'middleware' => config('livewire-quiz.middleware', ['web']),
], function () {
    Route::get('/', QuizList::class)->name('livewire-quiz.frontend.list');
    Route::get('/{quiz}', QuizTake::class)->name('livewire-quiz.frontend.take');
});

Route::group([
    'prefix' => config('livewire-quiz.route_prefix', 'quiz').'/admin',
    'middleware' => config('livewire-quiz.admin_middleware', ['web', 'auth']),
], function () {
    Route::get('/', QuizManager::class)->name('livewire-quiz.admin.quizzes');
});
