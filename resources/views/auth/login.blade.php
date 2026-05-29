<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-slate-900">Welcome back</h1>
        <p class="mt-2 text-slate-500">Sign in to keep your team projects on track.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
            <input id="email" class="cs-input mt-2" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@university.edu" />
            @error('email')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="text-sm font-medium text-slate-700">Password</label>
            <input id="password" class="cs-input mt-2" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            @error('password')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="inline-flex items-center gap-2 text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="cs-link" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="cs-primary-btn w-full">
            Log in
        </button>
    </form>

    <p class="mt-6 text-sm text-slate-600">
        New to CollabSphere? <a class="cs-link" href="{{ route('register') }}">Create an account</a>
    </p>
</x-guest-layout>
