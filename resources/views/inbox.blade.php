@extends('layouts.app')

@section('title', 'Inbox')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 anim-fade-up flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color:var(--text)">
            <i class="bi bi-tray" style="color:var(--purple-500);"></i> Inbox
        </h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:.875rem">
            Tasks with no due date
        </p>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn-purple btn d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Task
    </a>
</div>

@if($tasks->isEmpty())
    <div class="glass-card p-5 text-center anim-fade-up anim-delay-1">
        <i class="bi bi-inbox" style="font-size:3rem;color:var(--purple-400);opacity:.6;"></i>
        <p class="mt-3 mb-2 fw-semibold" style="color:var(--text)">Your inbox is clear!</p>
        <p style="color:var(--text-muted);font-size:.875rem">All tasks have a due date assigned.</p>
        <a href="{{ route('tasks.create') }}" class="btn-purple btn mt-1">
            <i class="bi bi-plus-circle me-1"></i> Create Task
        </a>
    </div>
@else
    <div class="glass-card p-3 anim-fade-up anim-delay-1">
        <div class="table-wrap">
            <table class="task-table">
                <thead>
                    <tr>
                        <th style="width:48px;"></th>
                        <th>Title</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $i => $task)
                    @php $priorityColors = ['high'=>'#ef4444','medium'=>'#f59e0b','low'=>'#10b981']; @endphp
                    <tr class="anim-fade-up"
                        style="animation-delay: {{ ($i * 0.06) + 0.1 }}s;{{ $task->is_completed ? 'opacity:0.65;' : '' }}"
                        data-task-id="{{ $task->id }}">

                        {{-- Toggle completion checkbox --}}
                        <td style="width:48px;">
                            <input type="checkbox"
                                   class="task-toggle-check"
                                   data-id="{{ $task->id }}"
                                   data-route="{{ route('tasks.toggle', $task) }}"
                                   {{ $task->is_completed ? 'checked' : '' }}
                                   title="{{ $task->is_completed ? 'Mark incomplete' : 'Mark complete' }}"
                                   style="width:1.25rem;height:1.25rem;cursor:pointer;accent-color:var(--purple-500);">
                        </td>

                        {{-- Title + priority badge + category chips --}}
                        <td>
                            <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                {{-- Priority badge --}}
                                <span class="badge rounded-pill"
                                      style="background:{{ $priorityColors[$task->priority] ?? '#8b5cf6' }};font-size:.65rem;padding:.25rem .55rem;">
                                    {{ ucfirst($task->priority ?? 'medium') }}
                                </span>
                                {{-- Title --}}
                                <span class="fw-semibold"
                                      style="color:var(--text);{{ $task->is_completed ? 'text-decoration:line-through;' : '' }}">
                                    {{ $task->title }}
                                </span>
                                {{-- Category chips --}}
                                @foreach($task->categories as $cat)
                                <span class="badge rounded-pill ms-1"
                                      style="background:{{ $cat->color }}20;color:{{ $cat->color }};border:1px solid {{ $cat->color }}40;font-size:.65rem;">
                                    {{ $cat->name }}
                                </span>
                                @endforeach
                                @if(($task->subtasks_count ?? 0) > 0)
                                <span class="badge rounded-pill ms-1"
                                      title="{{ $task->completed_subtasks_count }}/{{ $task->subtasks_count }} subtasks"
                                      style="background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.25);font-size:.65rem;">
                                    <i class="bi bi-check2-square me-1"></i>{{ $task->completed_subtasks_count }}/{{ $task->subtasks_count }}
                                </span>
                                @endif
                            </div>
                            @if($task->description)
                                <div class="text-truncate d-none d-md-block"
                                     style="color:var(--text-muted);font-size:.8rem;max-width:260px;">
                                    {{ $task->description }}
                                </div>
                            @endif
                        </td>

                        {{-- Status badge --}}
                        <td>
                            @if($task->is_completed)
                                <span class="badge-completed status-badge"><i class="bi bi-check2 me-1"></i>Done</span>
                            @else
                                <span class="badge-incomplete status-badge"><i class="bi bi-clock me-1"></i>Pending</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="text-end" style="white-space:nowrap;">
                            @if(!$task->is_completed)
                                <x-snooze-dropdown :task="$task" />
                            @endif
                            <a href="{{ route('tasks.edit', $task) }}"
                               class="btn btn-sm btn-ghost mx-1"
                               title="Edit task">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <button type="button"
                                    class="btn btn-sm delete-task-btn"
                                    data-route="{{ route('tasks.destroy', $task) }}"
                                    title="Delete task"
                                    style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:12px;padding:.35rem .85rem;font-weight:500;">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-1">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:40px;height:40px;border-radius:50%;background:rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:1.1rem;"></i>
                    </span>
                    <h5 class="modal-title fw-bold mb-0" id="deleteTaskModalLabel" style="color:var(--text)">
                        Delete Task
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="filter:none;opacity:.6;"></button>
            </div>
            <div class="modal-body" style="color:var(--text-muted);">
                Are you sure you want to delete this task? This action cannot be undone.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTaskForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn"
                            style="background:linear-gradient(135deg,#ef4444,#dc2626);border:none;color:#fff;border-radius:12px;padding:.5rem 1.25rem;font-weight:600;">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    /* ── Toggle Completion Checkbox ── */
    document.querySelectorAll('.task-toggle-check').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var route         = this.dataset.route;
            var originalState = !this.checked;
            this.disabled     = true;
            var self          = this;

            fetch(route, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                self.checked = data.is_completed;

                var row   = self.closest('tr');
                var badge = row.querySelector('.status-badge');

                if (badge) {
                    if (data.is_completed) {
                        badge.className   = 'badge-completed status-badge';
                        badge.innerHTML   = '<i class="bi bi-check2 me-1"></i>Done';
                        row.style.opacity = '0.65';
                    } else {
                        badge.className   = 'badge-incomplete status-badge';
                        badge.innerHTML   = '<i class="bi bi-clock me-1"></i>Pending';
                        row.style.opacity = '';
                    }
                }

                var titleSpan = row.querySelector('td .fw-semibold');
                if (titleSpan) {
                    titleSpan.style.textDecoration = data.is_completed ? 'line-through' : '';
                }
            })
            .catch(function () {
                self.checked = originalState;
                if (typeof window.toast === 'function') {
                    window.toast('Could not update task. Please try again.', 'error');
                }
            })
            .finally(function () { self.disabled = false; });
        });
    });

    /* ── Delete Modal ── */
    var deleteModal = document.getElementById('deleteTaskModal');
    var deleteForm  = document.getElementById('deleteTaskForm');

    document.querySelectorAll('.delete-task-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var route = this.dataset.route;
            if (deleteForm) {
                deleteForm.action = route;
            }
            if (deleteModal) {
                var bsModal = new bootstrap.Modal(deleteModal);
                bsModal.show();
            }
        });
    });

})();
</script>
@endpush
