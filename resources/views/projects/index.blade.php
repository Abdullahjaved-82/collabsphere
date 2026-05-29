<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">Your Projects</h2>
                @if (auth()->user()->teams->isNotEmpty())
                    <p class="mt-1 text-sm text-slate-500">{{ auth()->user()->teams->first()->name }}</p>
                @endif
            </div>
            <a href="{{ route('projects.create') }}" class="cs-primary-btn">
                + New Project
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        @if ($projects->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 px-6 text-center">
                <svg width="120" height="120" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-6 opacity-50">
                    <path d="M100 20L180 60V140L100 180L20 140V60L100 20Z" stroke="#CBD5E1" stroke-width="2" fill="none"/>
                    <path d="M100 60L140 85V135L100 160L60 135V85L100 60Z" stroke="#CBD5E1" stroke-width="2" fill="none"/>
                    <circle cx="100" cy="100" r="8" fill="#CBD5E1"/>
                </svg>
                <h3 class="text-2xl font-semibold text-slate-900 mb-2">No projects yet</h3>
                <p class="text-slate-500 mb-6 max-w-md">Create your first project to get started organizing your team's work.</p>
                <a href="{{ route('projects.create') }}" class="cs-primary-btn">Create Project</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project['id']) }}" class="group">
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="h-1 bg-gradient-to-r {{ match($project['status']) {
                                'active' => 'from-indigo-500 to-indigo-600',
                                'completed' => 'from-emerald-500 to-emerald-600',
                                'planning' => 'from-amber-500 to-amber-600',
                                default => 'from-slate-400 to-slate-500'
                            } }}"></div>

                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-slate-900 group-hover:text-indigo-600 transition mb-1">{{ $project['title'] }}</h3>
                                <p class="text-xs text-slate-500 mb-4">{{ $project['team']->name }}</p>

                                @if ($project['description'])
                                    <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $project['description'] }}</p>
                                @endif

                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-slate-700">Progress</span>
                                        <span class="text-xs font-semibold text-indigo-600">{{ $project['progress'] }}%</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-600 transition-all duration-500" style="width: {{ $project['progress'] }}%;"></div>
                                    </div>
                                </div>

                                <div class="flex gap-2 mb-4">
                                    @if ($project['task_stats']['todo'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-xs font-medium text-slate-700">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>
                                            {{ $project['task_stats']['todo'] }}
                                        </span>
                                    @endif
                                    @if ($project['task_stats']['in_progress'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-100 text-xs font-medium text-blue-700">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            {{ $project['task_stats']['in_progress'] }}
                                        </span>
                                    @endif
                                    @if ($project['task_stats']['done'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-100 text-xs font-medium text-emerald-700">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ $project['task_stats']['done'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                    <div class="flex items-center -space-x-2">
                                        @foreach ($project['members'] as $member)
                                            <img src="{{ $member->avatar ? asset('storage/' . $member->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($member->name) }}" 
                                                 alt="{{ $member->name }}"
                                                 class="h-8 w-8 rounded-full border-2 border-white object-cover"
                                                 title="{{ $member->name }}">
                                        @endforeach
                                        @if ($project['more_members'] > 0)
                                            <div class="h-8 w-8 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-700">
                                                +{{ $project['more_members'] }}
                                            </div>
                                        @endif
                                    </div>

                                    @if ($project['deadline'])
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-50 text-xs font-medium text-slate-600">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="5" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M9 4V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M15 4V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                            {{ $project['deadline']->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
