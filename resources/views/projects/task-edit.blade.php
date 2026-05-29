<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-3xl font-semibold text-slate-900">Edit Task</h2>
            <p class="text-slate-500 mt-1">{{ $project->title }}</p>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200 p-8">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-6">
                @csrf @method('PATCH')

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-900 mb-2">Task Title *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title', $task->title) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        required
                    >
                    @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-900 mb-2">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                    >{{ old('description', $task->description) }}</textarea>
                    @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-900 mb-2">Status *</label>
                        <select 
                            id="status" 
                            name="status" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            required
                        >
                            <option value="todo" {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ old('status', $task->status) === 'review' ? 'selected' : '' }}>Review</option>
                            <option value="done" {{ old('status', $task->status) === 'done' ? 'selected' : '' }}>Done</option>
                        </select>
                        @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-slate-900 mb-2">Priority *</label>
                        <select 
                            id="priority" 
                            name="priority" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            required
                        >
                            <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority', $task->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('priority')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-slate-900 mb-2">Due Date</label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        >
                        @error('due_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-slate-900 mb-2">Assign To</label>
                        <select 
                            id="assigned_to" 
                            name="assigned_to" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        >
                            <option value="">Unassigned</option>
                            @foreach ($project->team->users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-3 pt-6 border-t border-slate-200">
                    <button type="submit" class="cs-primary-btn">Update Task</button>
                    <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline ml-auto" onsubmit="return confirm('Delete this task?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-700 border border-red-300 rounded-lg hover:bg-red-50 transition">Delete</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
