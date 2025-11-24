# Livewire Quiz Package

Simple reusable quiz module for Laravel 10/11 with Livewire 3.

## Features

- Admin Livewire CRUD for quizzes, questions and options.
- Frontend Livewire quiz list and take flow.
- Attempts + answers tracking.
- Trait for attaching attempts to your User model.
- Publishable config, views and migrations so you can fully customize blades.

## Installation

1. Add the package to `composer.json` (or local path):

```json
"repositories": [
  {
    "type": "path",
    "url": "../livewire-quiz"
  }
]
```

```bash
composer require acme/livewire-quiz:"*"
```

2. Publish assets:

```bash
php artisan vendor:publish --tag=livewire-quiz-config
php artisan vendor:publish --tag=livewire-quiz-views
php artisan vendor:publish --tag=livewire-quiz-migrations
php artisan migrate
```

3. Ensure Livewire 3 is installed and set up.

4. Add the trait to your `User` model if you want helpers:

```php
use Acme\LivewireQuiz\Traits\HasQuizAttempts;

class User extends Authenticatable
{
    use HasQuizAttempts;
}
```

5. Routes:

- Frontend list: `/quiz`
- Take quiz: `/quiz/{quiz}`
- Admin manager: `/quiz/admin` (web + auth middleware by default)

## Customizing

- Change route prefix or middleware in `config/livewire-quiz.php`.
- Edit published blades in `resources/views/vendor/livewire-quiz`.
