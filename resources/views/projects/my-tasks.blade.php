<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">My Tasks</h2>
    </x-slot>

    <div class="py-8">
        <div class="mb-8">
            <div class="flex gap-3 mb-6">
                <button class="px-4 py-2 rounded-lg font-medium transition" 
                        data-filter="assigned" 
                        onclick="filterTasks('assigned')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium">
                    Assigned to Me
                </button>
                <button class="px-4 py-2 rounded-lg font-medium transition hover:bg-slate-100" 
                        data-filter="pending" 
                        onclick="filterTasks('pending')">
                    Pending Approval
                </button>
                <button class="px-4 py-2 rounded-lg font-medium transition hover:bg-slate-100" 
                        data-filter="available" 
                        onclick="filterTasks('available')">
                    Available to Claim
                </button>
            </div>
        </div>

        @php
            // Tasks assigned to current user
            $assignedTasks = auth()->user()->assignedTasks()
                ->with('project.team')
                ->where('status', '!=', 'done')
                ->orderBy('due_date')
                ->get();
            
            // Tasks claimed by user but waiting approval
            $pendingTasks = \App\Models\Task::with('project.team')
                ->where('requested_by', auth()->id())
                ->whereNull('assigned_to')
                ->where('status', '!=', 'done')
                ->orderBy('due_date')
                ->get();
            
            // Unassigned tasks in user's teams (not requested by anyone)
            $teamIds = auth()->user()->teams()->pluck('team_id')->toArray();
            $availableTasks = \App\Models\Task::with('project.team')
                ->whereIn('project_id', \App\Models\Project::whereIn('team_id', $teamIds)->pluck('id'))
                ->whereNull('assigned_to')
                ->whereNull('requested_by')
                ->where('status', '!=', 'done')
                ->orderBy('due_date')
                ->get();
        @endphp

        @if ($assignedTasks->isEmpty() && $availableTasks->isEmpty() && $pendingTasks->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <h4 class="text-lg font-semibold text-slate-900 mb-2">No work assigned</h4>
                <p class="text-slate-600">Check back soon or look for available tasks to claim.</p>
            </div>
        @else
            <div class="grid gap-6">
                <!-- Assigned to Me Section -->
                <div data-status="assigned">
                    @if ($assignedTasks->isNotEmpty())
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Assigned to Me ({{ $assignedTasks->count() }})</h3>
                        @foreach ($assignedTasks as $task)
                            <a href="{{ route('tasks.show', $task) }}" class="group">
                                <div class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md transition-all hover:border-indigo-300 mb-3">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-900 group-hover:text-indigo-600 transition">{{ $task->title }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ $task->project->title }} • {{ $task->project->team->name }}</p>
                                        </div>
                                        <span class="px-2 py-1 rounded text-xs font-medium flex-shrink-0 {{ match($task->status) {
                                            'todo' => 'bg-slate-100 text-slate-700',
                                            'in_progress' => 'bg-blue-100 text-blue-700',
                                            'review' => 'bg-purple-100 text-purple-700',
                                            'done' => 'bg-emerald-100 text-emerald-700',
                                        } }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs px-2 py-1 rounded-full {{ match($task->priority) {
                                            'low' => 'bg-gray-100 text-gray-700',
                                            'medium' => 'bg-yellow-100 text-yellow-700',
                                            'high' => 'bg-orange-100 text-orange-700',
                                            'critical' => 'bg-red-100 text-red-700',
                                        } }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        @if ($task->due_date)
                                            <span class="text-xs text-slate-500">
                                                {{ $task->due_date->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="text-slate-500 text-center py-8">No tasks assigned to you yet.</p>
                    @endif
                </div>

                <!-- Pending Approval Section -->
                <div data-status="pending" class="hidden">
                    @if ($pendingTasks->isNotEmpty())
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">⏳ Pending Approval ({{ $pendingTasks->count() }})</h3>
                        @foreach ($pendingTasks as $task)
                            <div class="bg-amber-50 rounded-xl border border-amber-200 p-4 mb-3">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $task->project->title }} • {{ $task->project->team->name }}</p>
                                        <p class="text-xs text-amber-700 mt-2">Awaiting team leader approval...</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs px-2 py-1 rounded-full {{ match($task->priority) {
                                        'low' => 'bg-gray-100 text-gray-700',
                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                        'high' => 'bg-orange-100 text-orange-700',
                                        'critical' => 'bg-red-100 text-red-700',
                                    } }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if ($task->due_date)
                                        <span class="text-xs text-slate-500">
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-slate-500 text-center py-8">No pending approvals.</p>
                    @endif
                </div>

                <!-- Available to Claim Section -->
                <div data-status="available" class="hidden">
                    @if ($availableTasks->isNotEmpty())
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Available to Claim ({{ $availableTasks->count() }})</h3>
                        @foreach ($availableTasks as $task)
                            <div class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md transition-all mb-3">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $task->project->title }} • {{ $task->project->team->name }}</p>
                                    </div>
                                    <form action="{{ route('tasks.claim', $task) }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                                            Claim
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs px-2 py-1 rounded-full {{ match($task->priority) {
                                        'low' => 'bg-gray-100 text-gray-700',
                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                        'high' => 'bg-orange-100 text-orange-700',
                                        'critical' => 'bg-red-100 text-red-700',
                                    } }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if ($task->due_date)
                                        <span class="text-xs text-slate-500">
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-slate-500 text-center py-8">No available tasks to claim.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        function filterTasks(status) {
            document.querySelectorAll('[data-status]').forEach(el => el.classList.add('hidden'));
            document.querySelector(`[data-status="${status}"]`)?.classList.remove('hidden');
            
            document.querySelectorAll('[data-filter]').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('hover:bg-slate-100', 'text-slate-700');
            });
            const activeBtn = document.querySelector(`[data-filter="${status}"]`);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-600', 'text-white');
                activeBtn.classList.remove('hover:bg-slate-100', 'text-slate-700');
            }
        }
        
        // Show "Assigned to Me" by default
        filterTasks('assigned');
    </script>
</x-app-layout>
