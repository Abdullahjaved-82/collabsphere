<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">{{ $team->name }}</h2>
    </x-slot>

    <div class="py-8 space-y-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-8">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Team Details</h3>
            @if ($team->description)
                <p class="text-slate-600 mb-6">{{ $team->description }}</p>
            @endif

            <div class="mb-6">
                <p class="text-sm text-slate-500 mb-2">Invite Code</p>
                <div class="flex items-center gap-3">
                    <code class="px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 font-mono font-semibold text-slate-900">{{ $team->invite_code }}</code>
                    <button type="button" 
                            onclick="navigator.clipboard.writeText('{{ $team->invite_code }}'); this.textContent = '✓ Copied'; setTimeout(() => this.textContent = 'Copy', 2000)"
                            class="px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-medium hover:bg-indigo-200 transition">
                        Copy
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-2">Share this code with your teammates to invite them.</p>
            </div>

        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-8">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Members ({{ $team->users()->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($team->users as $member)
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200">
                        <img src="{{ $member->avatar ? asset('storage/' . $member->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($member->name) . '&size=64' }}" 
                             alt="{{ $member->name }}"
                             class="h-10 w-10 rounded-full object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 text-sm truncate">{{ $member->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $member->email }}</p>
                            <span class="inline-flex mt-1 px-2 py-0.5 rounded text-xs font-medium {{ $member->pivot->role === 'leader' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($member->pivot->role) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-8">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Projects ({{ $team->projects->count() }})</h3>
            @if ($team->projects->isEmpty())
                <p class="text-slate-500 text-center py-8">No projects yet. Create a new project to get started.</p>
                <div class="text-center">
                    <a href="{{ route('projects.create') }}" class="cs-primary-btn inline-block">Create Project</a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($team->projects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $project->title }}</p>
                                <p class="text-xs text-slate-500">{{ $project->tasks()->count() }} tasks</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ match($project->status) {
                                'active' => 'bg-indigo-100 text-indigo-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'planning' => 'bg-amber-100 text-amber-700',
                            } }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
