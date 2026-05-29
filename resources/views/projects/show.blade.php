<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $project->title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $project->team->name }}</p>
            </div>
            @if (auth()->id() === $project->created_by)
                <div class="flex gap-3">
                    <a href="{{ route('projects.kanban', $project) }}" class="px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-medium hover:bg-indigo-200 transition">📊 Kanban</a>
                    <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">Edit</a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Delete this project?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg bg-rose-100 text-rose-700 font-medium hover:bg-rose-200 transition">Delete</button>
                    </form>
                </div>
            @else
                <div class="flex gap-3">
                    <a href="{{ route('projects.kanban', $project) }}" class="px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-medium hover:bg-indigo-200 transition">📊 Kanban</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-8">
            <div class="grid grid-cols-3 gap-6 mb-8">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ match($project->status) {
                        'active' => 'bg-indigo-100 text-indigo-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                        'planning' => 'bg-amber-100 text-amber-700',
                    } }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Deadline</p>
                    <p class="font-medium text-slate-900">{{ $project->deadline ? $project->deadline->format('M d, Y') : 'No deadline' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Created by</p>
                    <p class="font-medium text-slate-900">{{ $project->creator->name }}</p>
                </div>
            </div>

            @if ($project->description)
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-slate-900 mb-2">Description</h3>
                    <p class="text-slate-600">{{ $project->description }}</p>
                </div>
            @endif

            <div class="border-t border-slate-200 pt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Tasks ({{ $project->tasks->count() }})</h3>
                    @if (auth()->id() === $project->created_by)
                        <a href="{{ route('tasks.create', $project) }}" class="cs-primary-btn text-sm">+ Add Task</a>
                    @endif
                </div>
                @if ($project->tasks->isEmpty())
                    <p class="text-slate-500 text-center py-8">No tasks yet. <a href="{{ route('tasks.create', $project) }}" class="text-indigo-600 font-medium hover:text-indigo-700">Create one now</a>.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($project->tasks as $task)
                            @if (auth()->id() === $project->created_by)
                                <a href="{{ route('tasks.edit', $task) }}" class="group">
                            @else
                                <div class="group">
                            @endif
                                <div class="flex items-center justify-between gap-3 p-3 rounded-lg hover:bg-indigo-50 border border-transparent hover:border-indigo-200 transition">
                                    <div class="flex items-center gap-3 flex-1">
                                        <span class="px-2 py-1 rounded text-xs font-medium flex-shrink-0 {{ match($task->status) {
                                            'todo' => 'bg-slate-100 text-slate-700',
                                            'in_progress' => 'bg-blue-100 text-blue-700',
                                            'review' => 'bg-purple-100 text-purple-700',
                                            'done' => 'bg-emerald-100 text-emerald-700',
                                        } }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                        <div class="flex-1">
                                            <p class="font-medium text-slate-900 group-hover:text-indigo-600 transition">{{ $task->title }}</p>
                                            @if ($task->description)
                                                <p class="text-xs text-slate-500 line-clamp-1">{{ $task->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-xs px-2 py-1 rounded-full {{ match($task->priority) {
                                            'low' => 'bg-gray-100 text-gray-700',
                                            'medium' => 'bg-yellow-100 text-yellow-700',
                                            'high' => 'bg-orange-100 text-orange-700',
                                            'critical' => 'bg-red-100 text-red-700',
                                        } }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        @if ($task->assignedUser)
                                            <div class="flex items-center gap-2 px-2 py-1 rounded bg-indigo-50 border border-indigo-200">
                                                <img src="{{ $task->assignedUser->avatar ? asset('storage/' . $task->assignedUser->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($task->assignedUser->name) . '&size=32' }}" 
                                                     alt="{{ $task->assignedUser->name }}"
                                                     class="h-5 w-5 rounded-full object-cover">
                                                <span class="text-xs font-medium text-slate-700">{{ $task->assignedUser->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs px-2 py-1 rounded bg-slate-100 text-slate-600">Unassigned</span>
                                        @endif
                                    </div>
                                </div>
                            @if (auth()->id() === $project->created_by)
                                </a>
                            @else
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Pending Claims Section -->
            @php
                $pendingClaims = $project->tasks()->whereNotNull('requested_by')->whereNull('assigned_to')->with('requestedByUser')->get();
            @endphp
            @if ($pendingClaims->isNotEmpty())
                <div class="border-t border-slate-200 pt-8 mt-8">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">⏳ Pending Claim Approvals ({{ $pendingClaims->count() }})</h3>
                    <div class="space-y-3">
                        @foreach ($pendingClaims as $task)
                            <div class="flex items-center justify-between gap-4 p-4 rounded-lg border border-amber-200 bg-amber-50">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900">{{ $task->title }}</p>
                                    @if ($task->requestedByUser)
                                        <p class="text-xs text-amber-700 mt-1">
                                            Claimed by: <span class="font-medium">{{ $task->requestedByUser->name }}</span>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('tasks.approveClaim', $task) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 rounded text-xs font-medium bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition">
                                            Approve
                                        </button>
                                    </form>
                                    <a href="{{ route('tasks.edit', $task) }}" class="px-3 py-2 rounded text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Reject
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
