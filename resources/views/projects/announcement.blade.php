<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-3xl font-semibold text-slate-900">Send Announcement</h2>
            <p class="mt-1 text-sm text-slate-500">Share important updates with your team</p>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                    <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                    <ul class="text-sm text-red-800 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('messages.sendAnnouncement') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Team Selection -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Select Team</label>
                    <select name="team_id" required class="cs-input w-full">
                        <option value="">Choose a team...</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ (isset($teamId) && $teamId == $team->id) || old('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }} ({{ $team->users->count() }} members)
                            </option>
                        @endforeach
                    </select>
                    @error('team_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" required maxlength="100" placeholder="Announcement title..." 
                           value="{{ old('subject') }}"
                           class="cs-input w-full">
                    <p class="text-xs text-slate-500 mt-1">Max 100 characters</p>
                    @error('subject')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Body -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Message <span class="text-red-500">*</span></label>
                    <textarea name="body" required maxlength="2000" placeholder="Write your announcement..." rows="10"
                              class="cs-input w-full resize-none"
                              style="border: 1.5px solid #E2E8F0; border-radius: 10px; padding: 12px; font-size: 14px;">{{ old('body') }}</textarea>
                    <p class="text-xs text-slate-500 mt-1"><span class="char-count">{{ strlen(old('body', '')) }}</span>/2000 characters</p>
                    @error('body')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pin Toggle -->
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-900">Pin this announcement</label>
                        <p class="text-xs text-slate-500">Pinned announcements appear at the top</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- Preview Recipients -->
                @if (isset($teamId))
                    <div class="p-4 bg-slate-50 rounded-lg">
                        <p class="text-sm font-semibold text-slate-900 mb-3">Recipients:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($teams->find($teamId)?->users ?? [] as $member)
                                <div class="flex items-center gap-2 px-3 py-1 bg-white rounded-full border border-slate-200">
                                    <img src="{{ $member->avatar ? asset('storage/' . $member->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($member->name) . '&size=24' }}" 
                                         alt="{{ $member->name }}"
                                         class="h-6 w-6 rounded-full object-cover">
                                    <span class="text-sm text-slate-700">{{ $member->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Buttons -->
                <div class="flex gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('messages.inbox') }}" class="flex-1 px-6 py-3 rounded-lg border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-violet-600 text-white font-medium hover:shadow-lg transition">
                        📢 Send Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelector('textarea[name="body"]').addEventListener('input', function() {
            document.querySelector('.char-count').textContent = this.value.length;
        });

        // Update recipients when team changes
        document.querySelector('select[name="team_id"]').addEventListener('change', function() {
            if (this.value) {
                location.href = '?team_id=' + this.value;
            }
        });
    </script>
</x-app-layout>
