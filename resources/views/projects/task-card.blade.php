<div class="kanban-task bg-white rounded-lg border border-slate-200 p-4 cursor-grab hover:shadow-md transition select-none touch-none"
     data-task-id="{{ $task->id }}"
     data-status="{{ $task->status }}"
     data-position="{{ $task->position }}"
     data-assigned-to="{{ $task->assigned_to }}"
     style="user-select: none; touch-action: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;">
    
    <!-- Priority Badge -->
    <div class="flex justify-between items-start mb-2 pointer-events-none">
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ match($task->priority) {
            'low' => 'bg-gray-100 text-gray-700',
            'medium' => 'bg-indigo-100 text-indigo-700',
            'high' => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-red-100 text-red-700',
        } }}">
            {{ ucfirst($task->priority) }}
        </span>
    </div>
    
    <!-- Task Title -->
    <a href="{{ route('tasks.show', $task) }}" class="group pointer-events-auto" onclick="event.stopPropagation();">
        <p class="font-medium text-slate-900 group-hover:text-indigo-600 transition line-clamp-2">
            {{ $task->title }}
        </p>
    </a>
    
    <!-- Task Description Preview -->
    @if ($task->description)
        <p class="text-xs text-slate-500 mt-2 line-clamp-2 pointer-events-none">{{ $task->description }}</p>
    @endif
    
    <!-- Assignee Avatar -->
    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between pointer-events-none">
        @if ($task->assignedUser)
            <div class="flex items-center gap-2">
                <img src="{{ $task->assignedUser->avatar ? asset('storage/' . $task->assignedUser->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($task->assignedUser->name) . '&size=32' }}" 
                     alt="{{ $task->assignedUser->name }}"
                     class="h-6 w-6 rounded-full object-cover"
                     title="{{ $task->assignedUser->name }}">
                <span class="text-xs font-medium text-slate-600">{{ $task->assignedUser->name }}</span>
            </div>
        @else
            <span class="text-xs text-slate-400">Unassigned</span>
        @endif
        
        @if ($task->due_date)
            <span class="text-xs text-slate-400">
                {{ $task->due_date->format('M d') }}
            </span>
        @endif
    </div>
</div>
