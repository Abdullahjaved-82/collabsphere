<div class="kanban-task bg-white rounded-xl border border-slate-200 p-5 cursor-grab select-none touch-none shadow-sm"
     data-task-id="{{ $task->id }}"
     data-status="{{ $task->status }}"
     data-position="{{ $task->position }}"
     data-assigned-to="{{ $task->assigned_to }}"
     style="user-select: none; touch-action: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; border-left: 4.5px solid {{ match($task->priority) {
         'low' => '#CBD5E1',
         'medium' => '#6366F1',
         'high' => '#F97316',
         'critical' => '#EF4444',
     } }};">
    
    <!-- Header: Priority Badge & Categories if any -->
    <div class="flex justify-between items-center mb-3.5 pointer-events-none">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ match($task->priority) {
            'low' => 'badge-priority-low',
            'medium' => 'badge-priority-medium',
            'high' => 'badge-priority-high',
            'critical' => 'badge-priority-critical',
        } }}">
            <span class="h-1.5 w-1.5 rounded-full {{ match($task->priority) {
                'low' => 'bg-slate-400',
                'medium' => 'bg-indigo-500',
                'high' => 'bg-orange-500',
                'critical' => 'bg-red-500',
            } }}"></span>
            {{ ucfirst($task->priority) }}
        </span>
        
        <!-- Subtle options indicator or status representation -->
        <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
    </div>
    
    <!-- Title -->
    <a href="{{ route('tasks.show', $task) }}" class="group pointer-events-auto block" onclick="event.stopPropagation();">
        <h4 class="font-semibold text-slate-800 leading-snug group-hover:text-indigo-600 transition duration-200 line-clamp-2" style="font-size: 14.5px;">
            {{ $task->title }}
        </h4>
    </a>
    
    <!-- Description -->
    @if ($task->description)
        <p class="text-xs text-slate-500 mt-2 line-clamp-2 pointer-events-none leading-relaxed">{{ $task->description }}</p>
    @endif
    
    <!-- Assignee & Due Date Footer -->
    <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between pointer-events-none">
        <!-- Assignee Info -->
        @if ($task->assignedUser)
            <div class="flex items-center gap-2">
                <div class="relative h-6 w-6">
                    <img src="{{ $task->assignedUser->avatar ? asset('storage/' . $task->assignedUser->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($task->assignedUser->name) . '&size=32' }}" 
                         alt="{{ $task->assignedUser->name }}"
                         class="h-6 w-6 rounded-full object-cover ring-2 ring-white"
                         title="{{ $task->assignedUser->name }}">
                </div>
                <span class="text-xs font-semibold text-slate-600">{{ $task->assignedUser->name }}</span>
            </div>
        @else
            <div class="flex items-center gap-1.5 text-slate-400">
                <span class="text-xs font-medium">Unassigned</span>
            </div>
        @endif
        
        <!-- Due Date Badge -->
        @if ($task->due_date)
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-slate-50 border border-slate-200 text-slate-500 text-xs font-medium" style="font-size: 11px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ $task->due_date->format('M d') }}
            </span>
        @endif
    </div>
</div>
