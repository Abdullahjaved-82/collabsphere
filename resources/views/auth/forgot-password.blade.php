<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-slate-900">Reset your password</h1>
        <p class="mt-2 text-slate-500">No problem. Just let us know your email address and we'll send you a password reset link.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
            <input id="email" class="cs-input mt-2" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@university.edu" />
            @error('email')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="cs-primary-btn w-full">
            Send Reset Link
        </button>
    </form>

    <p class="mt-6 text-sm text-slate-600">
        Remember your password? <a class="cs-link" href="{{ route('login') }}">Back to login</a>
    </p>
</x-guest-layout>
