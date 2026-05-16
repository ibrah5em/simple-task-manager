<x-guest-layout>
    <div class="auth-card">
        <h5 class="fw-bold mb-1" style="color:var(--text)">Create account</h5>
        <p class="mb-4" style="color:var(--text-muted);font-size:.875rem">Join Task Manager today</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-person"></i>
                    <input id="name" type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="John Doe"
                           value="{{ old('name') }}" required autofocus>
                </div>
                @error('name')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-envelope"></i>
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="you@example.com"
                           value="{{ old('email') }}" required>
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
                           placeholder="Min. 8 characters"
                           required autocomplete="new-password">
                </div>
                @error('password')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-shield-lock"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control"
                           placeholder="Repeat password"
                           required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </form>

        <div class="divider">or</div>

        <p class="text-center mb-0" style="font-size:.875rem;color:var(--text-muted)">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </p>
    </div>
</x-guest-layout>
