<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">Messages</h2>
                <p class="mt-1 text-sm text-slate-500">Manage conversations and announcements</p>
            </div>
            <div class="flex items-center gap-3">
                @if (Auth::user()->teams()->wherePivot('role', 'leader')->exists())
                    <a href="{{ route('messages.announcement') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, #7C3AED, #A78BFA); color: #FFFFFF; font-weight: 600; font-size: 14px; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 14px rgba(124,58,237,0.35); transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                        📢 Send Announcement
                    </a>
                @endif
                <button id="composeBtn" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: #FFFFFF; font-weight: 600; font-size: 14px; border-radius: 10px; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35); transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(99,102,241,0.45)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(99,102,241,0.35)';">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    New Message
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <!-- Tabs -->
        <div class="mb-6 flex gap-2 border-b border-slate-200">
            <button class="tab-btn active px-4 py-2 border-b-2 border-indigo-600 text-indigo-600 font-medium" data-tab="inbox">
                📬 Inbox
                @if ($unreadCount > 0)
                    <span class="ml-2 inline-block px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">{{ $unreadCount }}</span>
                @endif
            </button>
            <button class="tab-btn px-4 py-2 border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium" data-tab="sent">
                📤 Sent
            </button>
            <a href="{{ route('messages.announcements') }}" class="tab-link px-4 py-2 border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium no-underline">
                📢 Announcements
            </a>
        </div>

        <!-- Inbox Tab -->
        <div id="inbox-tab" class="tab-content">
            @if (count($messages) > 0)
                <div class="space-y-2">
                    @foreach ($messages as $message)
                        <a href="{{ route('messages.show', $message) }}" class="block p-4 rounded-lg border border-slate-200 hover:bg-slate-50 hover:border-indigo-300 transition group {{ !$message->read_at ? 'bg-indigo-50 border-indigo-200' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($message->sender->name) . '&size=32' }}" 
                                             alt="{{ $message->sender->name }}"
                                             class="h-8 w-8 rounded-full object-cover">
                                        <div>
                                            <p class="font-semibold {{ !$message->read_at ? 'font-bold text-indigo-900' : 'text-slate-900' }} group-hover:text-indigo-600">{{ $message->sender->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-700 line-clamp-2">{{ $message->body }}</p>
                                </div>
                                @if (!$message->read_at)
                                    <div class="ml-4 h-3 w-3 rounded-full bg-indigo-600 flex-shrink-0"></div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #EEF2FF, #E0E7FF); margin-bottom: 16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <p style="color: #64748B; font-size: 16px; font-weight: 500; margin-bottom: 4px;">No messages yet</p>
                    <p style="color: #94A3B8; font-size: 14px; margin-bottom: 20px;">Start a conversation with your team</p>
                    <button id="composeBtn2" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: #FFFFFF; font-weight: 600; font-size: 14px; border-radius: 10px; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35); transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(99,102,241,0.45)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(99,102,241,0.35)';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Send your first message
                    </button>
                </div>
            @endif
        </div>

        <!-- Sent Tab -->
        <div id="sent-tab" class="tab-content hidden">
            @if (isset($sent) && count($sent) > 0)
                <div class="space-y-2">
                    @foreach ($sent as $message)
                        <div class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    @if ($message->type === 'announcement')
                                        <p class="text-xs font-semibold text-indigo-600 mb-1">📢 ANNOUNCEMENT</p>
                                        <p class="font-semibold text-slate-900">{{ $message->subject }}</p>
                                    @else
                                        <p class="font-semibold text-slate-900">To: {{ $message->receiver->name ?? 'Unknown' }}</p>
                                    @endif
                                    <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $message->body }}</p>
                                    <p class="mt-2 text-xs text-slate-500">{{ $message->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <p class="text-slate-400 text-lg">📭 No sent messages</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Compose Modal -->
    <div id="composeModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-slate-900">New Message</h3>
                <button class="close-modal text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form id="composeForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">To:</label>
                    <select name="receiver_id" required class="cs-input w-full" style="border: 1.5px solid #E2E8F0; border-radius: 10px; padding: 12px; font-size: 14px;">
                        <option value="">Select team member...</option>
                        @foreach ($teamsWithMembers ?? [] as $team)
                            @if ($team->users->isNotEmpty())
                                <optgroup label="🏢 {{ $team->name }}">
                                    @foreach ($team->users as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                    <textarea name="body" required placeholder="Type your message..." rows="6" class="cs-input w-full resize-none" style="border: 1.5px solid #E2E8F0; border-radius: 10px; padding: 12px; font-size: 14px;" maxlength="2000"></textarea>
                    <p class="text-xs text-slate-500 mt-1"><span class="char-count">0</span>/2000 characters</p>
                </div>

                <div style="display: flex; gap: 12px; padding-top: 16px; border-top: 1px solid #E2E8F0;">
                    <button type="button" class="close-modal" style="flex: 1; padding: 12px 20px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: #FFFFFF; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#94A3B8';" onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#CBD5E1';">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; border-radius: 10px; border: none; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: #FFFFFF; font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35); transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(99,102,241,0.45)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(99,102,241,0.35)';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tabName = btn.dataset.tab;
                
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
                    b.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700');
                });
                btn.classList.add('active', 'border-indigo-600', 'text-indigo-600');
                btn.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700');

                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
                document.getElementById(tabName + '-tab').classList.remove('hidden');
            });
        });

        // Modal
        const composeModal = document.getElementById('composeModal');
        const composeBtns = document.querySelectorAll('#composeBtn, #composeBtn2');
        const closeModalBtns = document.querySelectorAll('.close-modal');

        composeBtns.forEach(btn => {
            btn.addEventListener('click', () => composeModal.classList.remove('hidden'));
        });

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => composeModal.classList.add('hidden'));
        });

        // Character count
        const textarea = document.querySelector('textarea[name="body"]');
        if (textarea) {
            textarea.addEventListener('input', function() {
                document.querySelector('.char-count').textContent = this.value.length;
            });
        }

        // Form submission
        const composeForm = document.getElementById('composeForm');
        if (composeForm) {
            composeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                
                try {
                    const response = await fetch('{{ route("messages.send") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    if (response.ok) {
                        composeModal.classList.add('hidden');
                        e.target.reset();
                        location.reload();
                    } else {
                        alert('Error sending message');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error sending message');
                }
            });
        }

        // Polling for unread count
        setInterval(async () => {
            try {
                const response = await fetch('{{ route("api.messages.unreadCount") }}');
                const data = await response.json();
                const badge = document.querySelector('.inbox-badge');
                if (badge) {
                    badge.textContent = data.unread_count;
                    if (data.unread_count === 0) {
                        badge.classList.add('hidden');
                    } else {
                        badge.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 30000);
    </script>
</x-app-layout>
