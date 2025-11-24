<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $quiz->title }}</h1>

    @if (! $completed)
        @if ($this->currentQuestion)
            <div class="border rounded p-4 bg-white shadow-sm">
                <div class="text-sm text-gray-600 mb-2">
                    Question {{ $currentIndex + 1 }} of {{ $quiz->questions->count() }}
                </div>

                <p class="font-semibold mb-4">
                    {{ $this->currentQuestion->question_text }}
                </p>

                @foreach ($this->currentQuestion->options as $option)
                    <label class="block mb-2">
                        <input type="radio" wire:click="selectOption({{ $option->id }})"
                               name="option_{{ $this->currentQuestion->id }}"
                               @if($selectedOption === $option->id) checked @endif>
                        <span class="ml-2">{{ $option->option_text }}</span>
                    </label>
                @endforeach

                @error('selectedOption')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror

                <div class="mt-4 flex justify-end">
                    <button wire:click="next"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ $currentIndex + 1 >= $quiz->questions->count() ? 'Finish' : 'Next' }}
                    </button>
                </div>
            </div>
        @else
            <p>No questions in this quiz yet.</p>
        @endif
    @else
        <div class="border rounded p-4 bg-white shadow-sm">
            <h2 class="text-xl font-semibold mb-2">Quiz Completed</h2>
            <p class="mb-2">
                Your score: <strong>{{ $score }}</strong>
            </p>
            <p class="text-sm text-gray-600">
                You can close this page or go back to the quiz list.
            </p>
            <a href="{{ route('livewire-quiz.frontend.list') }}"
               class="mt-4 inline-block px-3 py-2 bg-gray-800 text-white rounded">
                Back to quizzes
            </a>
        </div>
    @endif
</div>
