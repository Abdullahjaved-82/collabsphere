<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">{{ $project->title }}</h2>
                <p class="mt-1 text-sm font-semibold text-indigo-600 uppercase letter-spacing-1">{{ $project->team->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.show', $project) }}" class="px-4.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition duration-200 text-sm flex items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Back to Project
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <!-- Control Bar -->
        <div class="mb-6 flex justify-between items-center">
            <button id="refreshKanban" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 active:scale-95 transition duration-200 text-sm shadow-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Sync Board
            </button>
            
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Real-time active
            </div>
        </div>

        <!-- Kanban Board Scroll Container -->
        <div class="overflow-x-auto pb-6" id="kanbanContainer">
            <div class="flex gap-6" style="min-width: min-content; padding: 4px;">
                @foreach ($kanbanData as $column)
                    <div class="flex-shrink-0 flex flex-col" style="width: 320px;">
                        
                        <!-- Column Header -->
                        <div class="mb-3.5 flex items-center justify-between px-1">
                            <h3 class="text-sm font-bold uppercase tracking-wider {{ match($column['status']) {
                                'todo' => 'text-slate-500',
                                'in_progress' => 'text-indigo-600',
                                'review' => 'text-amber-600',
                                'done' => 'text-emerald-600',
                            } }}">
                                {{ $column['label'] }}
                            </h3>
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full {{ match($column['status']) {
                                'todo' => 'bg-slate-200/70 text-slate-600',
                                'in_progress' => 'bg-indigo-100 text-indigo-700',
                                'review' => 'bg-amber-100 text-amber-700',
                                'done' => 'bg-emerald-100 text-emerald-700',
                            } }} text-xs font-bold transition-all duration-300">
                                {{ $column['tasks']->count() }}
                            </span>
                        </div>

                        <!-- Column Body -->
                        <div class="kanban-column rounded-2xl p-4 flex-1 flex flex-col {{ match($column['status']) {
                            'todo' => 'column-todo',
                            'in_progress' => 'column-inprogress',
                            'review' => 'column-review',
                            'done' => 'column-done',
                        } }}"
                             data-status="{{ $column['status'] }}"
                             style="min-height: 580px;">

                            <!-- Task List Container -->
                            <div class="space-y-3.5 kanban-tasks flex-1" style="min-height: 250px; padding: 2px;">
                                @forelse ($column['tasks'] as $task)
                                    @include('projects.task-card', compact('task'))
                                @empty
                                    <div class="text-center py-20 text-slate-400 pointer-events-none empty-state">
                                        <div style="font-size: 24px; margin-bottom: 8px;">📭</div>
                                        <p class="text-xs font-semibold text-slate-400">Empty column</p>
                                        <p class="text-[10px] text-slate-300 mt-1">Drop tasks here</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Column Actions -->
                        @if ($isTeamLeader)
                            <button class="add-task-btn mt-3.5 w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl border border-dashed border-slate-300 text-slate-500 hover:text-indigo-600 hover:border-indigo-400 hover:bg-indigo-50/70 transition duration-200 text-sm font-semibold"
                                    data-status="{{ $column['status'] }}"
                                    data-project-id="{{ $project->id }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Add Task
                            </button>
                        @else
                            <div class="mt-3.5 w-full py-2.5 rounded-xl bg-slate-50 border border-slate-100 text-slate-400 text-xs font-semibold text-center cursor-not-allowed">
                                🔒 Read Only
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: white; border-radius: 16px; width: 100%; max-width: 480px; padding: 28px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); border: 1px solid #E2E8F0; margin: 16px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; display: flex; align-items: center; gap: 8px; margin: 0;">
                    ✨ Create New Kanban Task
                </h3>
                <button onclick="closeTaskModal()" style="background: none; border: none; font-size: 20px; color: #94A3B8; cursor: pointer; font-weight: bold; padding: 4px;">✕</button>
            </div>
            
            <form id="addTaskForm" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <input type="hidden" id="taskStatus" name="status" value="">
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Task Title</label>
                    <input type="text" name="title" required style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; box-sizing: border-box; padding: 10px 14px;" placeholder="e.g. Implement user authentication">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Description</label>
                    <textarea name="description" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; resize: none; box-sizing: border-box; padding: 10px 14px;" placeholder="Describe what needs to be done..." rows="3"></textarea>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Priority</label>
                    <select name="priority" required style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; padding: 10px 14px;">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #F1F5F9; margin-top: 24px;">
                    <button type="button" onclick="closeTaskModal()" style="flex: 1; padding: 12px 20px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: #FFFFFF; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; border-radius: 10px; border: none; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: #FFFFFF; font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35); transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Custom Scrollbars for Kanban Board */
        #kanbanContainer::-webkit-scrollbar {
            height: 10px;
        }
        #kanbanContainer::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 10px;
        }
        #kanbanContainer::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
            border: 2.5px solid #F1F5F9;
        }
        #kanbanContainer::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        /* Column Specific Gradients and Styling */
        .kanban-column {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1.5px solid #E2E8F0;
        }

        .column-todo {
            background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%);
        }
        .column-inprogress {
            background: linear-gradient(180deg, #F8FAFC 0%, #EEF2FF 100%);
            border-color: #E0E7FF;
        }
        .column-review {
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFBEB 100%);
            border-color: #FEF3C7;
        }
        .column-done {
            background: linear-gradient(180deg, #F8FAFC 0%, #ECFDF5 100%);
            border-color: #D1FAE5;
        }

        /* Task Cards Layout and Interactive States */
        .kanban-task {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border-color: #E2E8F0;
        }
        .kanban-task:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 8px -2px rgba(0, 0, 0, 0.04);
            border-color: #C7D2FE;
        }

        /* Dynamic Drag over visual state */
        .column-drag-over {
            background: rgba(99, 102, 241, 0.05) !important;
            border-color: #8B5CF6 !important;
            box-shadow: inset 0 0 16px rgba(99, 102, 241, 0.08) !important;
        }

        /* SortableJS Visual States during Drag */
        .sortable-ghost {
            opacity: 0.3 !important;
            border: 2px dashed #8B5CF6 !important;
            background: rgba(99, 102, 241, 0.05) !important;
            box-shadow: none !important;
        }
        .sortable-chosen {
            transform: scale(1.01);
        }
        .sortable-drag {
            opacity: 0.96 !important;
            transform: rotate(2deg) scale(1.03) !important;
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.25), 0 10px 10px -5px rgba(99, 102, 241, 0.15) !important;
            cursor: grabbing !important;
            border-color: #8B5CF6 !important;
        }

        /* Priority Badges styling */
        .badge-priority-low {
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }
        .badge-priority-medium {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #DBEAFE;
        }
        .badge-priority-high {
            background: #FFF7ED;
            color: #C2410C;
            border: 1px solid #FFEDD5;
        }
        .badge-priority-critical {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FEE2E2;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let currentProjectId = {{ $project->id }};
        let currentStatus = null;
        let draggedElement = null;
        let isTeamLeader = {{ $isTeamLeader ? 'true' : 'false' }};
        let currentUserId = {{ auth()->id() }};

        function initSortables() {
            document.querySelectorAll('.kanban-tasks').forEach(taskList => {
                Sortable.create(taskList, {
                    group: {
                        name: 'kanban',
                        pull: true,
                        put: true,
                    },
                    sort: true,
                    animation: 250,
                    easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    dragoverBubble: false,
                    invertSwap: true,
                    invertedSwapThreshold: 0.35,
                    swapThreshold: 0.55,
                    removeCloneOnHide: true,
                    fallbackOnBody: false,
                    scrollSpeed: 12,
                    scrollSensitivity: 35,
                    emptyInsertThreshold: 5,
                    touchStartThreshold: 3,
                    onStart: handleDragStart,
                    onEnd: handleDragEnd,
                    onMove: handleDragMove,
                    onAdd: handleDragAdd,
                });
            });
        }

        function canUserDragTask(taskElement) {
            if (isTeamLeader) return true;
            const assignedUserId = parseInt(taskElement.dataset.assignedTo || '0');
            return assignedUserId === currentUserId;
        }

        function handleDragAdd(evt) {
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
            if (emptyState) emptyState.style.display = 'none';
            
            evt.item.classList.add('dragging');
        }

        function handleDragMove(evt) {
            // Highlight current hovering column beautifully using class
            document.querySelectorAll('.kanban-column').forEach(col => col.classList.remove('column-drag-over'));
            
            if (evt.to) {
                const column = evt.to.closest('.kanban-column');
                if (column) column.classList.add('column-drag-over');
            }

            // Butter-smooth auto-scroll horizontally
            const container = document.getElementById('kanbanContainer');
            const mouse = evt.originalEvent;
            if (mouse) {
                const rect = container.getBoundingClientRect();
                if (mouse.clientX > rect.right - 100) {
                    container.scrollLeft += 24;
                } else if (mouse.clientX < rect.left + 100) {
                    container.scrollLeft -= 24;
                }
            }
        }

        function handleDragEnd(evt) {
            // Remove highlighting
            document.querySelectorAll('.kanban-column').forEach(col => {
                col.classList.remove('column-drag-over');
            });
            
            evt.item.classList.remove('dragging');

            const task = evt.item;
            const taskId = task.dataset.taskId;
            const newStatus = evt.to.closest('.kanban-column').dataset.status;
            const newPosition = Array.from(evt.to.querySelectorAll('.kanban-task')).indexOf(task);

            console.log(`Moving task ${taskId} to ${newStatus} position ${newPosition}`);

            // AJAX position update
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

        // Refresh button handler
        document.getElementById('refreshKanban').addEventListener('click', () => {
            location.reload();
        });

        // Poll for external changes every 10 seconds to keep clients fully in sync
        setInterval(() => {
            fetch(`/projects/{{ $project->id }}/kanban-data`)
                .then(res => res.json())
                .then(data => {
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

        // Initialize sortables on load
        initSortables();

        // Add Task Modals handlers
        function closeTaskModal() {
            document.getElementById('addTaskModal').style.display = 'none';
        }

        document.querySelectorAll('.add-task-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                currentStatus = btn.dataset.status;
                document.getElementById('taskStatus').value = currentStatus;
                document.getElementById('addTaskModal').style.display = 'flex';
            });
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
                    // Inject the new gorgeous task card
                    const column = document.querySelector(`[data-status="${currentStatus}"] .kanban-tasks`);
                    
                    // Remove empty states if present
                    const emptyState = column.querySelector('.empty-state');
                    if (emptyState) emptyState.remove();

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = result.html;
                    
                    const newCard = tempDiv.firstElementChild;
                    column.appendChild(newCard);

                    // Clear and close
                    e.target.reset();
                    closeTaskModal();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Failed to create task');
            }
        });

        // Close on backdrop click
        document.getElementById('addTaskModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('addTaskModal')) {
                closeTaskModal();
            }
        });
    </script>
</x-app-layout>
