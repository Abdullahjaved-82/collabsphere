<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-slate-900">Create new password</h1>
        <p class="mt-2 text-slate-500">Enter a new password to complete your password reset.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
            <input id="email" class="cs-input mt-2" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="you@university.edu" />
            @error('email')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-sm font-medium text-slate-700">New password</label>
            <input id="password" class="cs-input mt-2" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            @error('password')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="text-sm font-medium text-slate-700">Confirm password</label>
            <input id="password_confirmation" class="cs-input mt-2" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            @error('password_confirmation')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="cs-primary-btn w-full">
            Reset Password
        </button>
    </form>

    <p class="mt-6 text-sm text-slate-600">
        <a class="cs-link" href="{{ route('login') }}">Back to login</a>
    </p>
</x-guest-layout>
