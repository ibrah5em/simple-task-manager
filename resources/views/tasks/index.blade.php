@extends('layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 anim-fade-up flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--text)">My Tasks</h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:.875rem">
            {{ $tasks->total() }} task{{ $tasks->total() !== 1 ? 's' : '' }} total
        </p>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn-purple btn d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Task
    </a>
</div>

{{-- ── Search / Filter / Sort bar ── --}}
<div class="glass-card p-3 mb-3 anim-fade-up">
    <form method="GET" action="{{ route('tasks.index') }}" class="row g-2 align-items-end">
        {{-- Search --}}
        <div class="col-12 col-sm-4">
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Search tasks…" value="{{ $filters['q'] ?? '' }}">
        </div>
        {{-- Status filter --}}
        <div class="col-6 col-sm-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="active"    {{ ($filters['status'] ?? '') === 'active'    ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="overdue"   {{ ($filters['status'] ?? '') === 'overdue'   ? 'selected' : '' }}>Overdue</option>
                <option value="week"      {{ ($filters['status'] ?? '') === 'week'      ? 'selected' : '' }}>Due this week</option>
            </select>
        </div>
        {{-- Category filter --}}
        @if($categories->isNotEmpty())
        <div class="col-6 col-sm-3">
            <select name="category" class="form-select form-select-sm">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>
        @endif
        {{-- Sort --}}
        <div class="col-6 col-sm-2">
            <select name="sort" class="form-select form-select-sm">
                <option value="">Default</option>
                <option value="priority" {{ ($filters['sort'] ?? '') === 'priority' ? 'selected' : '' }}>Priority</option>
                <option value="due_asc"  {{ ($filters['sort'] ?? '') === 'due_asc'  ? 'selected' : '' }}>Due ↑</option>
                <option value="due_desc" {{ ($filters['sort'] ?? '') === 'due_desc' ? 'selected' : '' }}>Due ↓</option>
                <option value="newest"   {{ ($filters['sort'] ?? '') === 'newest'   ? 'selected' : '' }}>Newest</option>
            </select>
        </div>
        <div class="col-6 col-sm-auto">
            <button type="submit" class="btn btn-purple btn-sm w-100">Filter</button>
        </div>
        @if(array_filter($filters ?? []))
        <div class="col-auto">
            <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        </div>
        @endif
    </form>
</div>

@if($tasks->isEmpty())
    <div class="glass-card p-5 text-center anim-fade-up anim-delay-1">
        <i class="bi bi-inbox" style="font-size:3rem;color:var(--purple-400);opacity:.6;"></i>
        <p class="mt-3 mb-2 fw-semibold" style="color:var(--text)">No tasks yet</p>
        <p style="color:var(--text-muted);font-size:.875rem">Create your first task to get started.</p>
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
                        <th style="width:40px;">
                            <input type="checkbox" id="selectAll"
                                   style="width:1.1rem;height:1.1rem;cursor:pointer;accent-color:var(--purple-500);">
                        </th>
                        <th style="width:48px;"></th>
                        <th>Title</th>
                        <th class="d-none d-sm-table-cell">Due Date</th>
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

                        {{-- Select checkbox --}}
                        <td style="width:40px;">
                            <input type="checkbox"
                                   class="task-select-check"
                                   value="{{ $task->id }}"
                                   style="width:1.1rem;height:1.1rem;cursor:pointer;accent-color:var(--purple-500);">
                        </td>

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

                        {{-- Title + priority badge + category chips + description hint --}}
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
                            </div>
                            @if($task->description)
                                <div class="text-truncate d-none d-md-block"
                                     style="color:var(--text-muted);font-size:.8rem;max-width:260px;">
                                    {{ $task->description }}
                                </div>
                            @endif
                        </td>

                        {{-- Due date badge --}}
                        <td class="d-none d-sm-table-cell" style="white-space:nowrap;">
                            @if($task->due_date)
                                @php
                                    $isOverdue = !$task->is_completed && $task->due_date->isPast();
                                    $isToday   = $task->due_date->isToday();
                                @endphp
                                <span style="
                                    display:inline-flex;align-items:center;gap:.3rem;
                                    font-size:.78rem;font-weight:600;
                                    padding:.25rem .65rem;border-radius:50px;
                                    background:{{ $isOverdue ? 'linear-gradient(135deg,#ef4444,#dc2626)' : ($isToday ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'rgba(124,58,237,.12)') }};
                                    color:{{ ($isOverdue || $isToday) ? '#fff' : 'var(--purple-600)' }};
                                ">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $task->due_date->format('M d, Y') }}
                                    @if($isOverdue)
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                    @endif
                                </span>
                            @else
                                <span style="color:var(--text-muted);font-size:.85rem;">—</span>
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
                            <a href="{{ route('tasks.edit', $task) }}"
                               class="btn btn-sm btn-ghost me-1"
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

    {{-- Pagination --}}
    <div class="mt-3 anim-fade-up anim-delay-2">
        {{ $tasks->links() }}
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

{{-- Bulk Action Bar --}}
<div id="bulkActionBar" style="
    position:fixed;
    bottom:1.5rem;
    left:50%;
    transform:translateX(-50%) translateY(120%);
    z-index:500;
    display:flex;
    align-items:center;
    gap:.75rem;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:50px;
    padding:.6rem 1.25rem;
    box-shadow:0 8px 32px rgba(0,0,0,0.25);
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    transition:transform .25s cubic-bezier(.34,1.56,.64,1);
    white-space:nowrap;
">
    <span id="bulkSelectedCount" style="font-weight:600;font-size:.875rem;color:var(--text);padding-right:.25rem;">0 selected</span>
    <div style="width:1px;height:20px;background:var(--border);"></div>
    <button id="bulkCompleteBtn" type="button"
            class="btn btn-sm"
            style="background:linear-gradient(135deg,#10b981,#059669);border:none;color:#fff;border-radius:50px;padding:.35rem 1rem;font-weight:600;font-size:.82rem;">
        <i class="bi bi-check2 me-1"></i> Complete
    </button>
    <button id="bulkIncompleteBtn" type="button"
            class="btn btn-sm"
            style="background:linear-gradient(135deg,#f59e0b,#d97706);border:none;color:#fff;border-radius:50px;padding:.35rem 1rem;font-weight:600;font-size:.82rem;">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Incomplete
    </button>
    <button id="bulkDeleteBtn" type="button"
            class="btn btn-sm"
            style="background:linear-gradient(135deg,#ef4444,#dc2626);border:none;color:#fff;border-radius:50px;padding:.35rem 1rem;font-weight:600;font-size:.82rem;">
        <i class="bi bi-trash me-1"></i> Delete
    </button>
</div>

@endsection

@push('scripts')
<script>
(function () {

    /* ────────────────────────────────────────────
       Helpers
    ──────────────────────────────────────────── */
    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function selectedIds() {
        return Array.from(
            document.querySelectorAll('.task-select-check:checked')
        ).map(function (cb) { return cb.value; });
    }

    /* ────────────────────────────────────────────
       Toggle Completion Checkbox (per-row PATCH)
    ──────────────────────────────────────────── */
    document.querySelectorAll('.task-toggle-check').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var route        = this.dataset.route;
            var originalState = !this.checked;
            this.disabled    = true;
            var self         = this;

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

                // Update title strikethrough
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

    /* ────────────────────────────────────────────
       Select-All + Bulk Action Bar
    ──────────────────────────────────────────── */
    var selectAllCb   = document.getElementById('selectAll');
    var bulkBar       = document.getElementById('bulkActionBar');
    var bulkCountSpan = document.getElementById('bulkSelectedCount');
    var rowCheckboxes = document.querySelectorAll('.task-select-check');

    function updateBulkBar() {
        var ids = selectedIds();
        var count = ids.length;

        if (bulkCountSpan) {
            bulkCountSpan.textContent = count + ' selected';
        }

        if (bulkBar) {
            if (count > 0) {
                bulkBar.style.transform = 'translateX(-50%) translateY(0)';
            } else {
                bulkBar.style.transform = 'translateX(-50%) translateY(120%)';
            }
        }

        // Sync select-all indeterminate state
        if (selectAllCb) {
            if (count === 0) {
                selectAllCb.checked       = false;
                selectAllCb.indeterminate = false;
            } else if (count === rowCheckboxes.length) {
                selectAllCb.checked       = true;
                selectAllCb.indeterminate = false;
            } else {
                selectAllCb.checked       = false;
                selectAllCb.indeterminate = true;
            }
        }
    }

    if (selectAllCb) {
        selectAllCb.addEventListener('change', function () {
            rowCheckboxes.forEach(function (cb) {
                cb.checked = selectAllCb.checked;
            });
            updateBulkBar();
        });
    }

    rowCheckboxes.forEach(function (cb) {
        cb.addEventListener('change', updateBulkBar);
    });

    /* ── Bulk API call ── */
    function bulkAction(action) {
        var ids = selectedIds();
        if (ids.length === 0) return;

        if (action === 'delete') {
            if (!confirm('Delete ' + ids.length + ' task' + (ids.length !== 1 ? 's' : '') + '? This cannot be undone.')) {
                return;
            }
        }

        fetch('/tasks/bulk/' + action, {
            method: 'POST',
            body: JSON.stringify({ ids: ids }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            }
        })
        .then(function (r) {
            if (!r.ok) { throw new Error('Server error'); }
            return r.json();
        })
        .then(function () {
            window.location.reload();
        })
        .catch(function () {
            if (typeof window.toast === 'function') {
                window.toast('Bulk action failed. Please try again.', 'error');
            }
        });
    }

    var bulkCompleteBtn   = document.getElementById('bulkCompleteBtn');
    var bulkIncompleteBtn = document.getElementById('bulkIncompleteBtn');
    var bulkDeleteBtn     = document.getElementById('bulkDeleteBtn');

    if (bulkCompleteBtn)   bulkCompleteBtn.addEventListener('click',   function () { bulkAction('complete'); });
    if (bulkIncompleteBtn) bulkIncompleteBtn.addEventListener('click', function () { bulkAction('incomplete'); });
    if (bulkDeleteBtn)     bulkDeleteBtn.addEventListener('click',     function () { bulkAction('delete'); });

    /* ────────────────────────────────────────────
       Delete Modal
    ──────────────────────────────────────────── */
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
