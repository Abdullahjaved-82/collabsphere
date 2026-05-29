<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $task->title }}</h2>
                <p class="text-slate-500 mt-1">{{ $task->project->title }} • {{ $task->project->team->name }}</p>
            </div>
            <a href="{{ route('projects.show', $task->project) }}" class="text-slate-600 hover:text-slate-900">← Back to Project</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl">
        <div class="bg-white rounded-2xl border border-slate-200 p-8 space-y-6">
            <!-- Task Status Update -->
            <form action="{{ route('tasks.update', $task) }}" method="POST" id="statusForm">
                @csrf @method('PATCH')
                
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-900 mb-3">Current Status</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach (['todo', 'in_progress', 'review', 'done'] as $status)
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $task->status === $status ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300' }}">
                                <input 
                                    type="radio" 
                                    name="status" 
                                    value="{{ $status }}"
                                    {{ $task->status === $status ? 'checked' : '' }}
                                    class="mr-2 cursor-pointer"
                                    onchange="updateTaskStatus(this)"
                                >
                                <span class="font-medium text-slate-900">
                                    {{ match($status) {
                                        'todo' => '📋 To Do',
                                        'in_progress' => '⏳ In Progress',
                                        'review' => '👀 Review',
                                        'done' => '✅ Done',
                                    } }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>

            <script>
                function updateTaskStatus(input) {
                    document.getElementById('statusForm').submit();
                }
            </script>

            <!-- Task Details -->
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Task Details</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Priority</p>
                        <span class="text-sm font-medium px-3 py-1 rounded-full {{ match($task->priority) {
                            'low' => 'bg-gray-100 text-gray-700',
                            'medium' => 'bg-yellow-100 text-yellow-700',
                            'high' => 'bg-orange-100 text-orange-700',
                            'critical' => 'bg-red-100 text-red-700',
                        } }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Due Date</p>
                        <p class="text-sm font-medium text-slate-900">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No deadline' }}
                        </p>
                    </div>
                </div>

                @if ($task->description)
                    <div>
                        <p class="text-sm text-slate-500 mb-2">Description</p>
                        <p class="text-slate-700 bg-slate-50 p-3 rounded-lg">{{ $task->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Project Progress -->
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Project Progress</h3>
                
                @php
                    $totalTasks = $task->project->tasks()->count();
                    $doneTasks = $task->project->tasks()->where('status', 'done')->count();
                    $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
                @endphp

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-900">Overall Progress</span>
                        <span class="text-sm font-bold text-indigo-600">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 h-full rounded-full transition-all duration-300" 
                             style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500">{{ $doneTasks }} of {{ $totalTasks }} tasks completed</p>
                </div>
            </div>

            <!-- Task Stats -->
            <div class="border-t border-slate-200 pt-6 grid grid-cols-3 gap-4">
                @php
                    $todoCount = $task->project->tasks()->where('status', 'todo')->count();
                    $inProgressCount = $task->project->tasks()->where('status', 'in_progress')->count();
                    $doneCount = $task->project->tasks()->where('status', 'done')->count();
                @endphp
                
                <div class="text-center">
                    <p class="text-2xl font-bold text-slate-700">{{ $todoCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">To Do</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $inProgressCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">In Progress</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $doneCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">Done</p>
                </div>
            </div>

            <!-- Return to My Tasks -->
            <div class="border-t border-slate-200 pt-6">
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                    ← Back to My Tasks
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
