<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">AI Assistant</h2>
        <p class="text-sm text-slate-500 mt-1">Select a project to launch the AI-powered task breakdown assistant</p>
    </x-slot>

    <div class="max-w-4xl">
        @if ($teams->isEmpty() || $teams->flatMap->projects->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="text-5xl mb-4">🤖</div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">No projects available</h3>
                <p class="text-sm text-slate-500 mb-6">Create a project first to start using the AI assistant.</p>
                <a href="{{ route('projects.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                    Create a Project
                </a>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($teams as $team)
                    @if ($team->projects->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-slate-200 p-6">
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">{{ $team->name }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($team->projects as $project)
                                    <a href="{{ route('projects.ai', $project) }}" 
                                       class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition">
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-lg flex-shrink-0 shadow-sm">
                                            🤖
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-slate-900 truncate group-hover:text-indigo-700 transition">{{ $project->title }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $project->tasks()->count() }} tasks · {{ ucfirst($project->status) }}</p>
                                        </div>
                                        <svg class="h-5 w-5 text-slate-300 group-hover:text-indigo-500 transition transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
