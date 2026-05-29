<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">My Tasks</h2>
                <p class="mt-1 text-sm text-slate-500">Manage and track your assigned task completions</p>
            </div>
            
            <!-- Quick Mini Stats Bar -->
            <div class="flex gap-4 flex-wrap">
                <div class="bg-white border border-slate-200 px-4 py-2 rounded-xl flex items-center gap-2.5 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase">Total:</span>
                    <span class="text-sm font-bold text-slate-800" id="stat-total">0</span>
                </div>
                <div class="bg-white border border-slate-200 px-4 py-2 rounded-xl flex items-center gap-2.5 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase">Active:</span>
                    <span class="text-sm font-bold text-slate-800" id="stat-active">0</span>
                </div>
                <div class="bg-white border border-slate-200 px-4 py-2 rounded-xl flex items-center gap-2.5 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase">Completed:</span>
                    <span class="text-sm font-bold text-slate-800" id="stat-completed">0</span>
                </div>
                <div class="bg-white border border-slate-200 px-4 py-2 rounded-xl flex items-center gap-2.5 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase">Overdue:</span>
                    <span class="text-sm font-bold text-slate-800" id="stat-overdue">0</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl">
        @php
            $user = auth()->user();
            $assignedTasks = $user->assignedTasks()
                ->with(['project.team'])
                ->get();
            
            // Sort tasks: overdue/due first, then by priority, then by due date
            $assignedTasks = $assignedTasks->sortBy(function($task) {
                return $task->due_date ? $task->due_date->timestamp : 9999999999;
            });

            $groupedTasks = $assignedTasks->groupBy(function($task) {
                return $task->project ? $task->project->id : 0;
            });
        @endphp

        @if ($assignedTasks->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-slate-200 shadow-sm max-w-xl mx-auto px-6">
                <div class="h-16 w-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">🎯</div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">No Tasks Assigned</h3>
                <p class="text-sm text-slate-500 mb-8 leading-relaxed">You don't have any tasks assigned to you right now. Check your projects or request tasks from your team leader.</p>
                <div class="flex justify-center">
                    <a href="{{ route('projects.index') }}" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl font-semibold shadow-md shadow-indigo-200 hover:opacity-90 transition">
                        📁 Browse Projects
                    </a>
                </div>
            </div>
        @else
            <!-- Filter Bar Tabs -->
            <div class="flex border-b border-slate-200 mb-8 gap-1 p-1 bg-slate-100/80 rounded-xl max-w-md shadow-inner">
                <button onclick="setFilter('all')" id="tab-all" class="flex-1 py-2 px-3 rounded-lg text-xs font-bold transition duration-200 flex items-center justify-center gap-1.5 active-tab-style">
                    <span>✨ All</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px]" id="count-all">0</span>
                </button>
                <button onclick="setFilter('todo')" id="tab-todo" class="flex-1 py-2 px-3 rounded-lg text-xs font-bold transition duration-200 text-slate-600 hover:bg-slate-200/50 flex items-center justify-center gap-1.5">
                    <span>📋 Todo</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px]" id="count-todo">0</span>
                </button>
                <button onclick="setFilter('in_progress')" id="tab-in_progress" class="flex-1 py-2 px-3 rounded-lg text-xs font-bold transition duration-200 text-slate-600 hover:bg-slate-200/50 flex items-center justify-center gap-1.5">
                    <span>⚡ In Progress</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px]" id="count-in_progress">0</span>
                </button>
                <button onclick="setFilter('overdue')" id="tab-overdue" class="flex-1 py-2 px-3 rounded-lg text-xs font-bold transition duration-200 text-slate-600 hover:bg-slate-200/50 flex items-center justify-center gap-1.5">
                    <span>⚠️ Overdue</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px]" id="count-overdue">0</span>
                </button>
            </div>

            <!-- Grouped Project Sections -->
            <div class="space-y-6" id="projects-container">
                @foreach ($groupedTasks as $projectId => $tasks)
                    @php
                        $project = $tasks->first()->project;
                        $projectName = $project ? $project->title : 'Independent Tasks';
                        $teamName = $project && $project->team ? $project->team->name : 'No Team';
                    @endphp
                    
                    <div class="project-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300" data-project-id="{{ $projectId }}">
                        <!-- Project Header (Collapsible Trigger) -->
                        <div onclick="toggleProject('{{ $projectId }}')" class="flex items-center justify-between p-5 bg-slate-50/60 hover:bg-slate-50 border-b border-slate-100 cursor-pointer select-none transition duration-150">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Collapse Indicator Chevron -->
                                <span class="chevron-icon text-slate-400 transform transition-transform duration-200" id="chevron-{{ $projectId }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 text-sm md:text-base truncate">{{ $projectName }}</h3>
                                    <p class="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5">
                                        <span>👥 {{ $teamName }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold font-sans tracking-wide" id="project-badge-{{ $projectId }}">
                                    {{ $tasks->count() }}
                                </span>
                            </div>
                        </div>

                        <!-- Project Body (Tasks List) -->
                        <div class="project-body transition-all duration-300 ease-in-out" id="body-{{ $projectId }}">
                            <div class="divide-y divide-slate-100">
                                @foreach ($tasks as $task)
                                    @php
                                        $isOverdue = false;
                                        if ($task->due_date && $task->status !== 'done') {
                                            $isOverdue = $task->due_date->isBefore(\Carbon\Carbon::today());
                                        }
                                        $isDone = $task->status === 'done';
                                    @endphp
                                    <div class="task-row flex items-center justify-between gap-4 p-4.5 transition hover:bg-slate-50/75 group/row {{ $isOverdue ? 'overdue-row-bg' : '' }} {{ $isDone ? 'task-done-dimmed' : '' }}" 
                                         data-task-id="{{ $task->id }}" 
                                         data-status="{{ $task->status }}"
                                         data-overdue="{{ $isOverdue ? 'true' : 'false' }}">
                                        
                                        <!-- Left block: Checkbox + Priority Dot + Title + Badges -->
                                        <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                            <!-- Checkbox to mark done inline -->
                                            <div class="flex items-center justify-center flex-shrink-0">
                                                <input type="checkbox" 
                                                       onclick="toggleTaskStatus(event, '{{ $task->id }}')" 
                                                       class="custom-checkbox h-5.5 w-5.5 rounded-full border border-slate-300 text-indigo-600 focus:ring-0 focus:ring-offset-0 cursor-pointer transition duration-200"
                                                       {{ $isDone ? 'checked' : '' }}>
                                            </div>

                                            <!-- Priority Dot -->
                                            <div class="flex-shrink-0">
                                                <span class="h-2.5 w-2.5 rounded-full block tooltip" 
                                                      style="background-color: {{ match($task->priority) {
                                                          'low' => '#94A3B8',
                                                          'medium' => '#F59E0B',
                                                          'high' => '#F97316',
                                                          'critical' => '#EF4444',
                                                      } }}"
                                                      title="Priority: {{ ucfirst($task->priority) }}">
                                                </span>
                                            </div>

                                            <!-- Task Title & Status Badge -->
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-700 hover:text-indigo-600 text-[14px] md:text-[15px] transition truncate leading-snug task-title">
                                                        {{ $task->title }}
                                                    </a>
                                                    
                                                    <!-- Inline Status Badge -->
                                                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded flex-shrink-0 font-sans tracking-wide border {{ match($task->status) {
                                                        'todo' => 'bg-slate-50 text-slate-500 border-slate-200',
                                                        'in_progress' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                        'review' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                        'done' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    } }}" id="status-badge-{{ $task->id }}">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right block: Due Date -->
                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            @if ($task->due_date)
                                                <div class="flex items-center gap-1.5 text-xs font-semibold font-sans">
                                                    @if ($isOverdue)
                                                        <span class="text-red-500 flex items-center gap-1 overdue-text">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                            </svg>
                                                            {{ $task->due_date->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 group-hover/row:text-slate-500 transition">
                                                            📅 {{ $task->due_date->format('M d, Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Beautiful Empty Filter State -->
            <div id="empty-state" class="hidden text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-sm max-w-xl mx-auto px-6 my-8">
                <div class="h-14 w-14 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">✨</div>
                <h4 class="text-base font-bold text-slate-800 mb-1">No tasks in this category</h4>
                <p class="text-xs text-slate-400 leading-relaxed">No tasks match your selected filter right now.</p>
            </div>
        @endif
    </div>

    <!-- Style and JS Scripts -->
    <style>
        /* Custom styled checkboxes */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            border-radius: 50%;
            display: inline-grid;
            place-content: center;
            border: 2px solid #CBD5E1;
            transition: all 0.2s ease;
        }
        .custom-checkbox:hover {
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .custom-checkbox:checked {
            background-color: #6366F1;
            border-color: #6366F1;
        }
        .custom-checkbox::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            clip-path: polygon(14% 44%, 0 58%, 38% 96%, 100% 16%, 86% 2%, 38% 70%);
            transform: scale(0);
            transform-origin: center;
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em white;
            background-color: white;
        }
        .custom-checkbox:checked::before {
            transform: scale(1);
        }

        /* Strikethrough & Dim animations */
        .task-done-dimmed {
            opacity: 0.65;
            transition: opacity 0.3s ease;
        }
        .task-done-dimmed .task-title {
            text-decoration: line-through;
            color: #94A3B8 !important;
        }

        /* Collapsible Chevron Rotation */
        .chevron-rotated {
            transform: rotate(-90deg);
        }

        /* Overdue Row Background */
        .overdue-row-bg {
            background-color: rgba(239, 68, 68, 0.04);
        }
        .overdue-row-bg:hover {
            background-color: rgba(239, 68, 68, 0.06) !important;
        }
        .overdue-text {
            color: #EF4444;
        }

        /* Tab Active Styling */
        .active-tab-style {
            background-color: #FFFFFF !important;
            color: #1E293B !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1) !important;
        }
    </style>

    <script>
        let currentFilter = 'all';
        const collapsedProjects = new Set();

        // Setup Page Counts and Initialize
        document.addEventListener('DOMContentLoaded', () => {
            updateTabCounts();
            setFilter('all');
        });

        // Toggle Collapsible Project Sections
        function toggleProject(projectId) {
            const body = document.getElementById(`body-${projectId}`);
            const chevron = document.getElementById(`chevron-${projectId}`);
            
            if (collapsedProjects.has(projectId)) {
                collapsedProjects.delete(projectId);
                body.classList.remove('hidden');
                chevron.classList.remove('chevron-rotated');
            } else {
                collapsedProjects.add(projectId);
                body.classList.add('hidden');
                chevron.classList.add('chevron-rotated');
            }
        }

        // Set Tab Filters
        function setFilter(filter) {
            currentFilter = filter;
            
            // Toggle active style on buttons
            const tabs = ['all', 'todo', 'in_progress', 'overdue'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-${t}`);
                if (btn) {
                    if (t === filter) {
                        btn.classList.add('active-tab-style');
                        btn.classList.remove('text-slate-600', 'hover:bg-slate-200/50');
                    } else {
                        btn.classList.remove('active-tab-style');
                        btn.classList.add('text-slate-600', 'hover:bg-slate-200/50');
                    }
                }
            });

            applyFilters();
        }

        // Apply filters dynamically in DOM
        function applyFilters() {
            const rows = document.querySelectorAll('.task-row');
            let totalVisibleTasks = 0;

            // Step 1: Filter each task row
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const isOverdue = row.getAttribute('data-overdue') === 'true';
                let visible = false;

                if (currentFilter === 'all') {
                    visible = true;
                } else if (currentFilter === 'todo') {
                    visible = (status === 'todo');
                } else if (currentFilter === 'in_progress') {
                    visible = (status === 'in_progress' || status === 'review');
                } else if (currentFilter === 'overdue') {
                    visible = (status !== 'done' && isOverdue);
                }

                if (visible) {
                    row.classList.remove('hidden');
                    totalVisibleTasks++;
                } else {
                    row.classList.add('hidden');
                }
            });

            // Step 2: Update Project card count badges and handle visibility
            let visibleProjects = 0;
            const projectCards = document.querySelectorAll('.project-card');
            
            projectCards.forEach(card => {
                const projectId = card.getAttribute('data-project-id');
                const cardRows = card.querySelectorAll('.task-row');
                let projectVisibleCount = 0;

                cardRows.forEach(row => {
                    if (!row.classList.contains('hidden')) {
                        projectVisibleCount++;
                    }
                });

                const badge = document.getElementById(`project-badge-${projectId}`);
                if (badge) {
                    badge.textContent = projectVisibleCount;
                }

                if (projectVisibleCount > 0) {
                    card.classList.remove('hidden');
                    visibleProjects++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Step 3: Show/Hide empty state
            const emptyState = document.getElementById('empty-state');
            if (visibleProjects === 0) {
                emptyState?.classList.remove('hidden');
            } else {
                emptyState?.classList.add('hidden');
            }
        }

        // Update counts dynamically based on current DOM elements
        function updateTabCounts() {
            const rows = document.querySelectorAll('.task-row');
            let countAll = 0;
            let countTodo = 0;
            let countInProgress = 0;
            let countOverdue = 0;
            let countDone = 0;

            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const isOverdue = row.getAttribute('data-overdue') === 'true';

                countAll++;
                if (status === 'todo') countTodo++;
                if (status === 'in_progress' || status === 'review') countInProgress++;
                if (status !== 'done' && isOverdue) countOverdue++;
                if (status === 'done') countDone++;
            });

            // Update badges in tabs
            document.getElementById('count-all').textContent = countAll;
            document.getElementById('count-todo').textContent = countTodo;
            document.getElementById('count-in_progress').textContent = countInProgress;
            document.getElementById('count-overdue').textContent = countOverdue;

            // Update mini header stats
            document.getElementById('stat-total').textContent = countAll;
            document.getElementById('stat-active').textContent = countTodo + countInProgress;
            document.getElementById('stat-completed').textContent = countDone;
            document.getElementById('stat-overdue').textContent = countOverdue;
        }

        // Handle inline completion checkbox toggling
        function toggleTaskStatus(event, taskId) {
            const checkbox = event.target;
            const taskRow = checkbox.closest('.task-row');
            const isChecked = checkbox.checked;
            
            // Optimistic UI updates
            const newStatus = isChecked ? 'done' : 'todo';
            taskRow.setAttribute('data-status', newStatus);
            
            if (isChecked) {
                taskRow.classList.add('task-done-dimmed');
            } else {
                taskRow.classList.remove('task-done-dimmed');
            }

            // Update status badge inline
            const badge = document.getElementById(`status-badge-${taskId}`);
            if (badge) {
                badge.textContent = isChecked ? 'Done' : 'Todo';
                
                // Update badge color classes
                badge.className = `inline-block text-[10px] font-bold px-2 py-0.5 rounded flex-shrink-0 font-sans tracking-wide border ` + 
                    (isChecked 
                        ? 'bg-emerald-50 text-emerald-600 border-emerald-100' 
                        : 'bg-slate-50 text-slate-500 border-slate-200');
            }

            // Send AJAX PATCH request
            fetch(`/tasks/${taskId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Failed to update task status');
                }
                
                // Success: Delay re-filtering slightly so the user sees the completed animation
                setTimeout(() => {
                    applyFilters();
                    updateTabCounts();
                }, 350);
            })
            .catch(error => {
                // Revert UI on failure
                checkbox.checked = !isChecked;
                taskRow.setAttribute('data-status', isChecked ? 'todo' : 'done');
                if (isChecked) {
                    taskRow.classList.remove('task-done-dimmed');
                } else {
                    taskRow.classList.add('task-done-dimmed');
                }
                if (badge) {
                    badge.textContent = isChecked ? 'Todo' : 'Done';
                    badge.className = `inline-block text-[10px] font-bold px-2 py-0.5 rounded flex-shrink-0 font-sans tracking-wide border ` + 
                        (isChecked 
                            ? 'bg-slate-50 text-slate-500 border-slate-200' 
                            : 'bg-emerald-50 text-emerald-600 border-emerald-100');
                }
                
                console.error('Error updating task status:', error);
                alert(error.message || 'Error occurred while updating task');
            });
        }
    </script>
</x-app-layout>
