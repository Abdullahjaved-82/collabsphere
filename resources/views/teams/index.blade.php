<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">Teams</h2>
    </x-slot>

    <div class="py-8">
        <div class="mb-8 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-900">Your Teams</h3>
            <a href="{{ route('teams.create') }}" class="cs-primary-btn">+ Create Team</a>
        </div>

        @if ($teams->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <h4 class="text-lg font-semibold text-slate-900 mb-2">No teams yet</h4>
                <p class="text-slate-600 mb-6">Create a team to start collaborating with your classmates.</p>
                <a href="{{ route('teams.create') }}" class="cs-primary-btn inline-block">Create Team</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($teams as $team)
                    <a href="{{ route('teams.show', $team) }}" class="group">
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <h4 class="text-lg font-semibold text-slate-900 group-hover:text-indigo-600 transition mb-1">{{ $team->name }}</h4>
                            <p class="text-xs text-slate-500 mb-3">Created by {{ $team->creator->name }}</p>
                            @if ($team->description)
                                <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $team->description }}</p>
                            @endif
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                <span class="text-sm font-medium text-slate-700">{{ $team->users()->count() }} members</span>
                                <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">{{ $team->projects()->count() }} projects</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Join Another Team Section -->
        <div class="mt-12">
            <div class="bg-white rounded-2xl border border-slate-200 p-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Join Another Team</h3>
                <form action="{{ route('teams.join') }}" method="POST" class="flex gap-2 items-center">
                    @csrf
                    <input 
                        type="text" 
                        name="invite_code" 
                        placeholder="Enter 8-character invite code" 
                        maxlength="8"
                        class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 placeholder-slate-400 uppercase"
                        required
                    >
                    <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium">Join</button>
                </form>
                @if ($errors->has('invite_code'))
                    <p class="text-red-600 text-sm mt-2">{{ $errors->first('invite_code') }}</p>
                @endif
                <p class="text-slate-500 text-xs mt-3">Ask a team member to share their invite code.</p>
            </div>
        </div>
    </div>
</x-app-layout>
