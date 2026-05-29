<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">
                    @if ($message->type === 'direct')
                        Message from {{ $message->sender->name }}
                    @else
                        📢 {{ $message->subject ?? 'Announcement' }}
                    @endif
                </h2>
                <p class="mt-1 text-sm text-slate-500">{{ $message->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <a href="{{ route('messages.inbox') }}" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl">
        <!-- Message Card -->
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <img src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($message->sender->name) . '&size=48' }}" 
                         alt="{{ $message->sender->name }}"
                         class="h-12 w-12 rounded-full object-cover">
                    <div class="flex-1">
                        <p class="font-semibold text-slate-900">{{ $message->sender->name }}</p>
                        <p class="text-sm text-slate-500">{{ $message->sender->email }}</p>
                    </div>
                    @if ($message->is_pinned)
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-medium">📌 Pinned</span>
                    @endif
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 min-h-48">
                <div class="whitespace-pre-wrap text-slate-700 leading-relaxed">
                    {!! nl2br(e($message->body)) !!}
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex items-center justify-between flex-wrap gap-4">
                <div class="flex gap-3">
                    @if ($message->type === 'announcement' && Auth::user()->teams()->where('team_id', $message->team_id)->wherePivot('role', 'leader')->exists())
                        <form action="{{ route('messages.pin', $message) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg {{ $message->is_pinned ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700' }} font-medium hover:opacity-90 transition">
                                {{ $message->is_pinned ? '📌 Unpin' : '📌 Pin' }}
                            </button>
                        </form>
                    @endif
                    
                    @if (Auth::id() === $message->sender_id)
                        <form action="{{ route('messages.destroy', $message) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this message?')" class="px-4 py-2 rounded-lg bg-red-100 text-red-700 font-medium hover:opacity-90 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    @endif
                </div>

                @if ($message->type === 'announcement')
                    <p class="text-sm text-slate-600">
                        Read by <strong>{{ $message->reads->where('user_id', '!=', $message->sender_id)->count() }}</strong> team member(s)
                    </p>
                @endif
            </div>
        </div>

        <!-- Read by List (for announcements) -->
        @if ($message->type === 'announcement' && $message->reads->where('user_id', '!=', $message->sender_id)->count() > 0)
            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Read by:</h3>
                <div class="space-y-2">
                    @foreach ($message->reads->where('user_id', '!=', $message->sender_id) as $read)
                        <div class="flex items-center gap-3">
                            <img src="{{ $read->user->avatar ? asset('storage/' . $read->user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($read->user->name) . '&size=32' }}" 
                                 alt="{{ $read->user->name }}"
                                 class="h-8 w-8 rounded-full object-cover">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $read->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $read->read_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
