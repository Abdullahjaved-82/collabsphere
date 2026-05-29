<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-slate-900">Create your account</h1>
        <p class="mt-2 text-slate-500">Start collaborating with your team in minutes.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5" x-data="{ avatarPreview: null }">
        @csrf

        <div>
            <label for="avatar" class="text-sm font-medium text-slate-700">Avatar</label>
            <div class="mt-3 flex items-center gap-4">
                <div class="h-20 w-20 rounded-full border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Avatar preview" class="h-full w-full object-cover" />
                    </template>
                    <template x-if="!avatarPreview">
                        <span class="text-xs text-slate-400">Upload</span>
                    </template>
                </div>
                <div class="flex-1">
                    <input id="avatar" class="cs-file-input" type="file" name="avatar" accept="image/*"
                        @change="const file = $event.target.files[0]; avatarPreview = file ? URL.createObjectURL(file) : null;" />
                    <p class="mt-2 text-xs text-slate-500">PNG or JPG up to 2MB.</p>
                </div>
            </div>
            @error('avatar')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="text-sm font-medium text-slate-700">Full name</label>
            <input id="name" class="cs-input mt-2" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ayesha Khan" />
            @error('name')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
            <input id="email" class="cs-input mt-2" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@university.edu" />
            @error('email')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="text-sm font-medium text-slate-700">Password</label>
            <input id="password" class="cs-input mt-2" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" />
            @error('password')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="text-sm font-medium text-slate-700">Confirm password</label>
            <input id="password_confirmation" class="cs-input mt-2" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" />
            @error('password_confirmation')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="cs-primary-btn w-full">
            Create account
        </button>
    </form>

    <p class="mt-6 text-sm text-slate-600">
        Already registered? <a class="cs-link" href="{{ route('login') }}">Sign in</a>
    </p>
</x-guest-layout>
