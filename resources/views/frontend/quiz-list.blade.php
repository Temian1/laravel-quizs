<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Available Quizzes</h1>

    <div class="mb-4">
        <input type="text" wire:model.debounce.500ms="search"
               class="w-full border rounded px-3 py-2"
               placeholder="Search quizzes...">
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($quizzes as $quiz)
            <div class="border rounded p-4 bg-white shadow-sm flex flex-col">
                <h2 class="font-semibold text-lg mb-1">{{ $quiz->title }}</h2>
                <p class="text-sm text-gray-700 flex-1">
                    {{ Str::limit($quiz->description, 140) }}
                </p>

                <div class="mt-3 flex items-center justify-between text-xs text-gray-600">
                    <span>{{ $quiz->questions_count ?? $quiz->questions->count() }} questions</span>
                    @if ($quiz->time_limit_minutes)
                        <span>{{ $quiz->time_limit_minutes }} mins</span>
                    @endif
                </div>

                <a href="{{ route('livewire-quiz.frontend.take', $quiz) }}"
                   class="mt-4 inline-block text-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Take Quiz
                </a>
            </div>
        @empty
            <p class="text-gray-600">No quizzes found.</p>
        @endforelse
    </div>
</div>
