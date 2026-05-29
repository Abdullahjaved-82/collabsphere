<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">Announcements</h2>
                <p class="mt-1 text-sm text-slate-500">Team updates and important messages</p>
            </div>
            @if (Auth::user()->teams()->wherePivot('role', 'leader')->exists())
                <a href="{{ route('messages.announcement') }}" class="px-4 py-2 rounded-lg bg-purple-600 text-white font-medium hover:bg-purple-700 transition">
                    📢 New Announcement
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <!-- Team Filter -->
        @if (count($teams) > 0)
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Filter by Team:</label>
                <select id="teamFilter" class="cs-input">
                    <option value="">All Teams</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Announcements Grid -->
        @if (count($messages) > 0)
            <div class="space-y-3">
                @foreach ($messages as $announcement)
                    <a href="{{ route('messages.show', $announcement) }}" class="block p-5 rounded-lg border-l-4 {{ $announcement->is_pinned ? 'border-l-amber-500 bg-amber-50 border border-amber-200' : 'border-l-slate-300 bg-white border border-slate-200' }} hover:shadow-lg transition group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Pinned Badge -->
                                @if ($announcement->is_pinned)
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-200 text-amber-800">
                                            📌 Pinned
                                        </span>
                                    </div>
                                @endif

                                <!-- Subject -->
                                <h3 class="font-semibold text-slate-900 text-lg group-hover:text-indigo-600 transition">
                                    {{ $announcement->subject }}
                                </h3>

                                <!-- Preview -->
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">
                                    {{ $announcement->body }}
                                </p>

                                <!-- Metadata -->
                                <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $announcement->sender->avatar ? asset('storage/' . $announcement->sender->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($announcement->sender->name) . '&size=28' }}" 
                                             alt="{{ $announcement->sender->name }}"
                                             class="h-7 w-7 rounded-full object-cover">
                                        <span class="text-xs text-slate-600">
                                            <strong>{{ $announcement->sender->name }}</strong> • {{ $announcement->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded">
                                        📖 Read by {{ $announcement->reads->where('user_id', '!=', $announcement->sender_id)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50">
                <p class="text-slate-400 text-lg mb-4">📭 No announcements</p>
                @if (Auth::user()->teams()->wherePivot('role', 'leader')->exists())
                    <p class="text-sm text-slate-500 mb-4">Be the first to share important updates with your team</p>
                    <a href="{{ route('messages.announcement') }}" class="inline-block px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                        📢 Create Announcement
                    </a>
                @else
                    <p class="text-sm text-slate-500">Check back soon for announcements from your team leads</p>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.getElementById('teamFilter').addEventListener('change', function() {
            const teamId = this.value;
            const url = new URL(window.location);
            if (teamId) {
                url.searchParams.set('team_id', teamId);
            } else {
                url.searchParams.delete('team_id');
            }
            window.location.href = url.toString();
        });
    </script>
</x-app-layout>
