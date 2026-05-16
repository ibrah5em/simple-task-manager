<x-guest-layout>
    <div class="auth-card">
        <h5 class="fw-bold mb-1" style="color:var(--text)">Welcome back</h5>
        <p class="mb-4" style="color:var(--text-muted);font-size:.875rem">Sign in to your account</p>

        @if(session('status'))
            <div class="alert alert-info rounded-3 py-2 px-3 mb-3" style="font-size:.875rem">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-envelope"></i>
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="you@example.com"
                           value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-lock"></i>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                </div>
                @error('password')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 d-flex align-items-center gap-2">
                <input type="checkbox" class="form-check-input mt-0" id="remember" name="remember"
                       style="accent-color:var(--purple-600)">
                <label class="form-check-label" for="remember" style="font-size:.875rem;color:var(--text-muted)">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-arrow-right-circle me-2"></i>Sign In
            </button>
        </form>

        <div class="divider">or</div>

        <p class="text-center mb-0" style="font-size:.875rem;color:var(--text-muted)">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link">Create one</a>
        </p>
    </div>
</x-guest-layout>
