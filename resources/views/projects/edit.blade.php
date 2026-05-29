<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold text-slate-900">Edit Project</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl border border-slate-200 p-8">
                <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                    @csrf @method('PUT')

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Project Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" required class="cs-input">
                        @error('title')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" class="cs-input">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            <select id="status" name="status" required class="cs-input">
                                <option value="planning" {{ $project->status === 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="deadline" class="block text-sm font-medium text-slate-700 mb-2">Deadline</label>
                            <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}" class="cs-input">
                            @error('deadline')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="submit" class="cs-primary-btn flex-1">Update Project</button>
                        <a href="{{ route('projects.show', $project) }}" class="flex-1 px-6 py-3 rounded-lg border border-slate-300 text-center font-medium text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
