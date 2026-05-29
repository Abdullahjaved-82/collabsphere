<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">Create Team</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl border border-slate-200 p-8">
                <form action="{{ route('teams.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Team Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Design Squad" class="cs-input">
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="What is this team working on?" class="cs-input">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="submit" class="cs-primary-btn flex-1">Create Team</button>
                        <a href="{{ route('teams.index') }}" class="flex-1 px-6 py-3 rounded-lg border border-slate-300 text-center font-medium text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
