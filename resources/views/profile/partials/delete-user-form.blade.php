<h5 class="fw-semibold mb-1" style="color:var(--text)">Delete Account</h5>
<p class="mb-3" style="color:var(--text-muted);font-size:.875rem">
    Once your account is deleted, all of its resources and data will be permanently deleted.
    Before deleting your account, please download any data or information that you wish to retain.
</p>

<button
    type="button"
    class="btn btn-danger"
    data-bs-toggle="modal"
    data-bs-target="#deleteAccountModal"
>
    Delete Account
</button>

{{-- Confirmation Modal --}}
<div
    class="modal fade"
    id="deleteAccountModal"
    tabindex="-1"
    aria-labelledby="deleteAccountModalLabel"
    aria-hidden="true"
    @if($errors->userDeletion->isNotEmpty()) data-bs-show="true" @endif
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header" style="border-bottom:1px solid var(--border)">
                <h5 class="modal-title fw-semibold" id="deleteAccountModalLabel" style="color:var(--text)">
                    Are you sure you want to delete your account?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" style="color:var(--text-muted);font-size:.875rem">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('delete')

                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Password</label>
                        <input
                            type="password"
                            id="delete_password"
                            name="password"
                            class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                        >
                        @foreach($errors->userDeletion->get('password') as $message)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('deleteAccountModal');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif
