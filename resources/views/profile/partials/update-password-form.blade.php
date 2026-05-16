<h5 class="fw-semibold mb-3" style="color:var(--text)">Update Password</h5>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">Current Password</label>
        <input
            type="password"
            id="update_password_current_password"
            name="current_password"
            class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
            autocomplete="current-password"
        >
        @foreach($errors->updatePassword->get('current_password') as $message)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @endforeach
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label">New Password</label>
        <input
            type="password"
            id="update_password_password"
            name="password"
            class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
            autocomplete="new-password"
        >
        @foreach($errors->updatePassword->get('password') as $message)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @endforeach
    </div>

    <div class="mb-4">
        <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
        <input
            type="password"
            id="update_password_password_confirmation"
            name="password_confirmation"
            class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
            autocomplete="new-password"
        >
        @foreach($errors->updatePassword->get('password_confirmation') as $message)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-purple">Update Password</button>
</form>

@if(session('status') === 'password-updated')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.toast('Password updated.', 'success');
    });
</script>
@endif
