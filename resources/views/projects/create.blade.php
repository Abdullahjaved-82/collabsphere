<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">Create Project</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl border border-slate-200 p-8">
                <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="team_id" class="block text-sm font-medium text-slate-700 mb-2">Team</label>
                        @if (isset($preselectedTeamId) && $preselectedTeamId)
                            @php $preselectedTeam = $teams->firstWhere('id', $preselectedTeamId); @endphp
                            <input type="hidden" name="team_id" value="{{ $preselectedTeamId }}">
                            <div class="cs-input bg-slate-50 text-slate-700 cursor-not-allowed flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>{{ $preselectedTeam ? $preselectedTeam->name : 'Selected Team' }}</span>
                            </div>
                        @else
                            <select id="team_id" name="team_id" required class="cs-input">
                                <option value="">Select a team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('team_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Project Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Mobile App Redesign" class="cs-input">
                        @error('title')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Brief description of the project..." class="cs-input">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            <select id="status" name="status" required class="cs-input">
                                <option value="planning">Planning</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="deadline" class="block text-sm font-medium text-slate-700 mb-2">Deadline</label>
                            <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}" class="cs-input">
                            @error('deadline')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="submit" class="cs-primary-btn flex-1">Create Project</button>
                        <a href="{{ isset($preselectedTeamId) && $preselectedTeamId ? route('teams.show', $preselectedTeamId) : route('projects.index') }}" class="flex-1 px-6 py-3 rounded-lg border border-slate-300 text-center font-medium text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
