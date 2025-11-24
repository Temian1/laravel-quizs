<?php

namespace Acme\LivewireQuiz;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Acme\LivewireQuiz\Http\Livewire\Admin\QuizManager;
use Acme\LivewireQuiz\Http\Livewire\Frontend\QuizList;
use Acme\LivewireQuiz\Http\Livewire\Frontend\QuizTake;

class LivewireQuizServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-quiz.php', 'livewire-quiz');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-quiz');

        Livewire::component('quiz-admin.manager', QuizManager::class);
        Livewire::component('quiz-frontend.list', QuizList::class);
        Livewire::component('quiz-frontend.take', QuizTake::class);

        $this->publishes([
            __DIR__.'/../config/livewire-quiz.php' => config_path('livewire-quiz.php'),
        ], 'livewire-quiz-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/livewire-quiz'),
        ], 'livewire-quiz-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'livewire-quiz-migrations');
    }

    public function register(): void
    {
        //
    }
}
