<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Quiz Manager</h1>

    @if (session('quiz_message'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
            {{ session('quiz_message') }}
        </div>
    @endif

    <div class="mb-6">
        <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + New Quiz
        </button>
    </div>

    @if ($showForm)
        <div class="mb-8 border rounded p-4 bg-white shadow-sm">
            <h2 class="text-lg font-semibold mb-3">{{ $quizId ? 'Edit Quiz' : 'Create Quiz' }}</h2>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" wire:model.defer="title" class="w-full border rounded px-2 py-1">
                    @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea wire:model.defer="description" class="w-full border rounded px-2 py-1" rows="3"></textarea>
                    @error('description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.defer="is_active" class="mr-2">
                        Active
                    </label>

                    <div>
                        <label class="block text-sm font-medium mb-1">Time limit (minutes)</label>
                        <input type="number" wire:model.defer="time_limit_minutes" class="border rounded px-2 py-1 w-24">
                        @error('time_limit_minutes') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold">Questions</h3>
                    <button wire:click="addQuestion" class="text-sm px-3 py-1 bg-gray-800 text-white rounded">
                        + Add Question
                    </button>
                </div>

                @foreach ($questions as $qIndex => $question)
                    <div class="mb-4 border rounded p-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold">Question {{ $qIndex + 1 }}</span>
                            <button wire:click="removeQuestion({{ $qIndex }})" class="text-xs text-red-600">
                                Remove
                            </button>
                        </div>

                        <div class="mb-2">
                            <input type="text" wire:model.defer="questions.{{ $qIndex }}.question_text"
                                   class="w-full border rounded px-2 py-1"
                                   placeholder="Question text">
                            @error('questions.' . $qIndex . '.question_text')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="text-xs">Points</label>
                            <input type="number" wire:model.defer="questions.{{ $qIndex }}.points"
                                   class="border rounded px-2 py-1 w-20">
                        </div>

                        <div class="mt-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Options</span>
                                <button wire:click="addOption({{ $qIndex }})"
                                        class="text-xs px-2 py-1 bg-gray-700 text-white rounded">
                                    + Add Option
                                </button>
                            </div>

                            @foreach ($question['options'] as $oIndex => $option)
                                <div class="flex items-center space-x-2 mb-1">
                                    <input type="text"
                                           wire:model.defer="questions.{{ $qIndex }}.options.{{ $oIndex }}.option_text"
                                           class="flex-1 border rounded px-2 py-1"
                                           placeholder="Option text">

                                    <label class="inline-flex items-center text-xs">
                                        <input type="checkbox"
                                               wire:model.defer="questions.{{ $qIndex }}.options.{{ $oIndex }}.is_correct"
                                               class="mr-1">
                                        Correct
                                    </label>

                                    <button wire:click="removeOption({{ $qIndex }}, {{ $oIndex }})"
                                            class="text-xs text-red-600">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-end space-x-2">
                <button wire:click="resetForm" class="px-3 py-1 border rounded">
                    Cancel
                </button>
                <button wire:click="save" class="px-4 py-1 bg-blue-600 text-white rounded">
                    Save
                </button>
            </div>
        </div>
    @endif

    <div class="border rounded bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left px-3 py-2">Title</th>
                    <th class="text-left px-3 py-2">Questions</th>
                    <th class="text-left px-3 py-2">Active</th>
                    <th class="text-left px-3 py-2">Updated</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quizzes as $quiz)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $quiz->title }}</td>
                        <td class="px-3 py-2">{{ $quiz->questions->count() }}</td>
                        <td class="px-3 py-2">
                            @if ($quiz->is_active)
                                <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Yes</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">No</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-600">{{ $quiz->updated_at }}</td>
                        <td class="px-3 py-2 text-right space-x-2">
                            <button wire:click="edit({{ $quiz->id }})"
                                    class="px-2 py-1 text-xs border rounded">
                                Edit
                            </button>
                            <button wire:click="delete({{ $quiz->id }})"
                                    onclick="return confirm('Delete this quiz?')"
                                    class="px-2 py-1 text-xs border rounded text-red-600">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-3 py-3 text-center text-sm text-gray-500" colspan="5">
                            No quizzes yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
