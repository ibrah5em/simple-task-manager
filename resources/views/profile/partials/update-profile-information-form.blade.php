<h5 class="fw-semibold mb-3" style="color:var(--text)">Profile Information</h5>

<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name) }}"
            required
            autofocus
            autocomplete="name"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email) }}"
            required
            autocomplete="username"
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="small mb-1" style="color:var(--text-muted)">
                    Your email address is unverified.
                </p>
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-ghost">
                        Click here to re-send the verification email.
                    </button>
                </form>
                @if (session('status') === 'verification-link-sent')
                    <p class="small mt-1" style="color:#10b981">
                        A new verification link has been sent to your email address.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="mb-4">
        <label for="phone" class="form-label">Phone <span class="text-muted fw-normal">(optional)</span></label>
        <input
            type="tel"
            id="phone"
            name="phone"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $user->phone ?? '') }}"
            autocomplete="tel"
        >
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-purple">Save Changes</button>
</form>

@if(session('status') === 'profile-updated')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.toast('Profile updated.', 'success');
    });
</script>
@endif
