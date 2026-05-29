<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $project->title }} — Kanban</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $project->team->name }}</p>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                ← Back to Project
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mb-4 flex gap-3">
            <button id="refreshKanban" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                🔄 Refresh Board
            </button>
        </div>

        <div class="overflow-x-auto pb-6" id="kanbanContainer">
            <div class="flex gap-6" style="min-width: min-content;">
                @foreach ($kanbanData as $column)
                    <div class="flex-shrink-0" style="width: 320px;">
                        <!-- Column Header -->
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold {{ match($column['status']) {
                                'todo' => 'text-slate-900',
                                'in_progress' => 'text-indigo-900',
                                'review' => 'text-amber-900',
                                'done' => 'text-emerald-900',
                            } }}">
                                {{ $column['label'] }}
                            </h3>
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full {{ match($column['status']) {
                                'todo' => 'bg-slate-200 text-slate-700',
                                'in_progress' => 'bg-indigo-200 text-indigo-700',
                                'review' => 'bg-amber-200 text-amber-700',
                                'done' => 'bg-emerald-200 text-emerald-700',
                            } }} text-xs font-bold">
                                {{ $column['tasks']->count() }}
                            </span>
                        </div>

                        <!-- Column Background -->
                        <div class="kanban-column bg-slate-50 rounded-2xl border-2 border-slate-200 p-4"
                             data-status="{{ $column['status'] }}"
                             style="min-height: 600px;">

                            <!-- Task List -->
                            <div class="space-y-3 kanban-tasks" style="min-height: 200px; padding: 4px; border-radius: 8px;">
                                @forelse ($column['tasks'] as $task)
                                    @include('projects.task-card', compact('task'))
                                @empty
                                    <div class="text-center py-16 text-slate-400 pointer-events-none empty-state">
                                        <p class="text-sm">📭 Drop tasks here</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Placeholder for drag-over -->
                            <div class="kanban-placeholder hidden border-2 border-dashed border-indigo-400 rounded-lg p-3 my-2 animate-pulse"
                                 style="display: none;">
                            </div>
                        </div>

                        <!-- Add Task Button - Only for Team Leaders -->
                        @if ($isTeamLeader)
                            <button class="mt-3 w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 transition text-sm font-medium add-task-btn"
                                    data-status="{{ $column['status'] }}"
                                    data-project-id="{{ $project->id }}">
                                + Add Task
                            </button>
                        @else
                            <div class="mt-3 w-full px-3 py-2 rounded-lg bg-slate-50 text-slate-400 text-sm font-medium text-center cursor-not-allowed">
                                📝 Lead only
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center"
         style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold text-slate-900 mb-4">Add New Task</h3>
            
            <form id="addTaskForm" class="space-y-4">
                @csrf
                <input type="hidden" id="taskStatus" name="status" value="">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                    <input type="text" name="title" required class="cs-input" placeholder="Task title">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" class="cs-input" placeholder="Optional description" rows="3"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Priority</label>
                    <select name="priority" required class="cs-input">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-6 border-t border-slate-200">
                    <button type="button" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 transition cancel-task-btn">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                        ✨ Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let currentProjectId = {{ $project->id }};
        let currentStatus = null;
        let draggedElement = null;
        let isTeamLeader = {{ $isTeamLeader ? 'true' : 'false' }};
        let currentUserId = {{ auth()->id() }};

        function initSortables() {
            // Initialize Sortable for each column
            const sortables = [];
            document.querySelectorAll('.kanban-tasks').forEach(taskList => {
                
                const sortable = Sortable.create(taskList, {
                    group: {
                        name: 'kanban',
                        pull: true,
                        put: true,
                    },
                    sort: true,
                    animation: 200,
                    easing: 'cubic-bezier(1, 0, 0, 1)',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    dragoverBubble: false,
                    invertSwap: true,
                    invertedSwapThreshold: 0.3,
                    swapThreshold: 0.5,
                    removeCloneOnHide: true,
                    fallbackOnBody: false,
                    scrollSpeed: 10,
                    scrollSensitivity: 30,
                    emptyInsertThreshold: 5,
                    touchStartThreshold: 3,
                    onStart: handleDragStart,
                    onEnd: handleDragEnd,
                    onMove: handleDragMove,
                    onAdd: handleDragAdd,
                });
                
                sortables.push(sortable);
            });
        }

        function canUserDragTask(taskElement) {
            if (isTeamLeader) return true;
            const assignedUserId = parseInt(taskElement.dataset.assignedTo || '0');
            return assignedUserId === currentUserId;
        }

        function handleDragAdd(evt) {
            // Handle when card is added to a new column
            console.log('Card added to column');
        }

        function handleDragStart(evt) {
            if (!canUserDragTask(evt.item)) {
                evt.preventDefault();
                alert('Only team leaders or the assigned person can move this task.');
                return;
            }

            draggedElement = evt.item;
            // Remove empty state message
            const emptyState = evt.from.querySelector('.empty-state');
            if (emptyState) emptyState.remove();
            
            evt.item.classList.add('dragging');
            evt.item.style.borderWidth = '2px';
            evt.item.style.borderColor = '#6366F1';
            evt.item.style.backgroundColor = '#EEF2FF';
        }

        function handleDragMove(evt) {
            // More aggressive drop detection
            if (evt.related && evt.related.classList && evt.related.classList.contains('kanban-task')) {
                // Dragging over another card
                if (evt.to) {
                    evt.to.closest('.kanban-column').style.backgroundColor = '#E0E7FF';
                    evt.to.closest('.kanban-column').style.borderColor = '#6366F1';
                    evt.to.closest('.kanban-column').style.borderWidth = '2px';
                }
            } else if (evt.to && evt.to.classList.contains('kanban-tasks')) {
                // Dragging over empty space in column
                const column = evt.to.closest('.kanban-column');
                if (column) {
                    column.style.backgroundColor = '#E0E7FF';
                    column.style.borderColor = '#6366F1';
                    column.style.borderWidth = '2px';
                }
            }

            // Auto-scroll horizontally
            const container = document.getElementById('kanbanContainer');
            const mouse = evt.originalEvent;
            if (mouse) {
                const rect = container.getBoundingClientRect();
                if (mouse.clientX > rect.right - 100) {
                    container.scrollLeft += 20;
                } else if (mouse.clientX < rect.left + 100) {
                    container.scrollLeft -= 20;
                }
            }
        }

        function handleDragEnd(evt) {
            // Remove highlighting
            document.querySelectorAll('.kanban-column').forEach(col => {
                col.style.backgroundColor = '';
                col.style.borderColor = '';
                col.style.borderWidth = '';
            });
            
            evt.item.classList.remove('dragging');
            evt.item.style.borderWidth = '';
            evt.item.style.borderColor = '';
            evt.item.style.backgroundColor = '';

            const task = evt.item;
            const taskId = task.dataset.taskId;
            const newStatus = evt.to.closest('.kanban-column').dataset.status;
            const newPosition = Array.from(evt.to.querySelectorAll('.kanban-task')).indexOf(task);

            console.log(`Moving task ${taskId} to ${newStatus} position ${newPosition}`);

            // AJAX update
            fetch(`/tasks/${taskId}/position`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    status: newStatus,
                    position: newPosition,
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    console.error('Update failed:', data);
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                setTimeout(() => location.reload(), 500);
            });
        }

        // Add custom styles for drag states
        const style = document.createElement('style');
        style.textContent = `
            .sortable-ghost {
                opacity: 0.3 !important;
                background-color: #E0E7FF !important;
            }
            .sortable-chosen {
                background-color: #EEF2FF !important;
                border: 2px solid #6366F1 !important;
            }
            .sortable-drag {
                opacity: 1 !important;
                transform: scale(1.05) !important;
                background-color: #EEF2FF !important;
                border: 2px solid #6366F1 !important;
                box-shadow: 0 15px 40px rgba(99,102,241,0.4) !important;
                z-index: 1000 !important;
                cursor: grabbing !important;
            }
        `;
        document.head.appendChild(style);

        // Refresh button handler
        document.getElementById('refreshKanban').addEventListener('click', () => {
            location.reload();
        });

        // Poll for external changes every 10 seconds
        setInterval(() => {
            fetch(`/projects/{{ $project->id }}/kanban-data`)
                .then(res => res.json())
                .then(data => {
                    // Check if any task has moved to different status
                    let needsRefresh = false;
                    document.querySelectorAll('.kanban-task').forEach(card => {
                        const currentStatus = card.closest('.kanban-column').dataset.status;
                        const taskId = card.dataset.taskId;
                        const task = data.tasks.find(t => t.id == taskId);
                        if (task && task.status !== currentStatus) {
                            needsRefresh = true;
                        }
                    });
                    if (needsRefresh) {
                        location.reload();
                    }
                })
                .catch(err => console.log('Poll error (expected):', err));
        }, 10000);

        // Initialize on page load
        initSortables();

        // Add Task Button Handlers
        document.querySelectorAll('.add-task-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                currentStatus = btn.dataset.status;
                document.getElementById('taskStatus').value = currentStatus;
                document.getElementById('addTaskModal').style.display = 'flex';
            });
        });

        document.querySelector('.cancel-task-btn').addEventListener('click', () => {
            document.getElementById('addTaskModal').style.display = 'none';
        });

        document.getElementById('addTaskForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            try {
                const res = await fetch(`/projects/${currentProjectId}/tasks-ajax`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(data),
                });

                const result = await res.json();
                if (result.success) {
                    // Add new task card to column
                    const column = document.querySelector(`[data-status="${currentStatus}"] .kanban-tasks`);
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = result.html;
                    column.appendChild(tempDiv.firstElementChild);

                    // Reset and close modal
                    e.target.reset();
                    document.getElementById('addTaskModal').style.display = 'none';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Failed to create task');
            }
        });

        // Close modal when clicking outside
        document.getElementById('addTaskModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('addTaskModal')) {
                document.getElementById('addTaskModal').style.display = 'none';
            }
        });
    </script>
</x-app-layout>
