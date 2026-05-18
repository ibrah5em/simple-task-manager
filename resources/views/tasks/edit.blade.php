@extends('layouts.app')

@section('content')
<style>
    .cat-pill-label {
        transition: border-color .15s, background .15s, color .15s;
        font-size: .875rem;
        font-weight: 500;
        color: var(--text);
        user-select: none;
    }
    .cat-pill-label:has(.cat-pill-check:checked) {
        border-color: var(--purple-500) !important;
        background: rgba(139,92,246,.12) !important;
        color: var(--purple-700);
    }
    [data-bs-theme="dark"] .cat-pill-label:has(.cat-pill-check:checked) {
        color: var(--purple-400);
    }
    .cat-pill-check {
        display: none;
    }
</style>
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <div class="d-flex align-items-center gap-3 mb-4 anim-fade-up">
            <a href="{{ route('tasks.index') }}" class="btn-ghost btn btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color:var(--text)">Edit Task</h4>
            </div>
        </div>

        <div class="glass-card p-4 anim-fade-up anim-delay-1">

            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="form-label">
                        Title <span style="color:#ef4444">*</span>
                    </label>
                    <div style="position:relative;">
                        <i class="bi bi-pencil" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;"></i>
                        <input id="title" type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               style="padding-left:2.4rem!important;"
                               placeholder="e.g. Prepare project report"
                               value="{{ old('title', $task->title) }}" required maxlength="255">
                    </div>
                    @error('title')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Optional — add details about this task...">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="due_date" class="form-label">Due Date</label>
                    <div style="position:relative;">
                        <i class="bi bi-calendar3" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;z-index:1;"></i>
                        <input id="due_date" type="date" name="due_date"
                               class="form-control @error('due_date') is-invalid @enderror"
                               style="padding-left:2.4rem!important;"
                               value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                    </div>
                    @error('due_date')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                        <option value="medium" {{ old('priority', $task->priority ?? 'medium')==='medium'?'selected':'' }}>Medium</option>
                        <option value="high"   {{ old('priority', $task->priority ?? 'medium')==='high'?'selected':'' }}>High</option>
                        <option value="low"    {{ old('priority', $task->priority ?? 'medium')==='low'?'selected':'' }}>Low</option>
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @php
                    $currentPreset = old('recurrence',
                        $task->recurrence_rule
                            ? app(\App\Services\RecurrenceService::class)->rruleToPreset($task->recurrence_rule)
                            : 'never'
                    );
                @endphp
                <div class="mb-4">
                    <label class="form-label">Repeats</label>
                    <select name="recurrence" class="form-select @error('recurrence') is-invalid @enderror">
                        <option value="never"    {{ $currentPreset==='never'    ? 'selected' : '' }}>Never</option>
                        <option value="daily"    {{ $currentPreset==='daily'    ? 'selected' : '' }}>Daily</option>
                        <option value="weekdays" {{ $currentPreset==='weekdays' ? 'selected' : '' }}>Weekdays (Mon–Fri)</option>
                        <option value="weekly"   {{ $currentPreset==='weekly'   ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly"  {{ $currentPreset==='monthly'  ? 'selected' : '' }}>Monthly</option>
                    </select>
                    <div class="form-text" style="font-size:.78rem;">Weekly/Monthly use the due date to determine the day.</div>
                    @error('recurrence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($categories->isNotEmpty())
                <div class="mb-4">
                    <label class="form-label">Categories</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($categories as $cat)
                        <label class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill cat-pill-label"
                               style="cursor:pointer;border:1px solid var(--border);background:var(--surface-2);">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                   {{ in_array($cat->id, old('categories', $task->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="form-check-input cat-pill-check" style="margin-top:0;">
                            <span style="background:{{ $cat->color }};width:10px;height:10px;border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                            {{ $cat->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ── Subtasks / Checklist ── --}}
                <div class="mb-4">
                    <label class="form-label">Subtasks</label>

                    <ul id="subtask-list" class="list-unstyled mb-2" style="min-height:4px;">
                        @foreach($task->subtasks as $sub)
                        <li class="subtask-item d-flex align-items-center gap-2 py-1 px-2 rounded mb-1"
                            data-id="{{ $sub->id }}"
                            style="background:var(--surface-2);border:1px solid var(--border);">
                            <span class="subtask-drag" style="cursor:grab;color:var(--text-muted);padding:.1rem .2rem;">
                                <i class="bi bi-grip-vertical"></i>
                            </span>
                            <input type="checkbox"
                                   class="subtask-toggle-check"
                                   data-id="{{ $sub->id }}"
                                   data-route="{{ route('subtasks.toggle', $sub) }}"
                                   {{ $sub->is_completed ? 'checked' : '' }}
                                   style="width:1.1rem;height:1.1rem;flex-shrink:0;accent-color:var(--purple-500);cursor:pointer;">
                            <span class="subtask-title flex-grow-1"
                                  style="font-size:.9rem;color:var(--text);{{ $sub->is_completed ? 'text-decoration:line-through;opacity:.6;' : '' }}">
                                {{ $sub->title }}
                            </span>
                            <button type="button"
                                    class="subtask-delete-btn btn btn-sm"
                                    data-id="{{ $sub->id }}"
                                    data-route="{{ route('subtasks.destroy', $sub) }}"
                                    style="padding:.1rem .4rem;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#ef4444;border-radius:6px;line-height:1;">
                                <i class="bi bi-x"></i>
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    {{-- Add subtask input --}}
                    <div class="d-flex gap-2">
                        <input id="new-subtask-input" type="text"
                               class="form-control form-control-sm"
                               placeholder="Add a subtask… (Enter to save)"
                               maxlength="255">
                        <button type="button" id="add-subtask-btn"
                                class="btn btn-sm"
                                style="background:linear-gradient(135deg,var(--purple-500),var(--purple-600));border:none;color:#fff;border-radius:10px;padding:.35rem .9rem;white-space:nowrap;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="glass-card-inner p-3 d-flex align-items-center gap-3">
                        <div class="form-check mb-0 d-flex align-items-center gap-2" style="margin:0;">
                            <input class="form-check-input" type="checkbox" name="is_completed" id="is_completed"
                                   value="1" {{ old('is_completed', $task->is_completed) ? 'checked' : '' }}
                                   style="width:1.2rem;height:1.2rem;border-color:var(--purple-500);cursor:pointer;margin-top:0;">
                            <label class="form-check-label fw-semibold mb-0" for="is_completed"
                                   style="color:var(--text);cursor:pointer;font-size:.95rem;">
                                Mark as completed
                            </label>
                        </div>
                        <span class="ms-auto" style="font-size:.8rem;color:var(--text-muted);">
                            Current:
                            @if($task->is_completed)
                                <span class="badge-completed" style="font-size:.7rem;"><i class="bi bi-check2 me-1"></i>Done</span>
                            @else
                                <span class="badge-incomplete" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>Pending</span>
                            @endif
                        </span>
                    </div>
                    @error('is_completed')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3 pt-2">
                    <button type="submit" class="btn-purple btn d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle"></i> Update Task
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn-ghost btn d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.4/Sortable.min.js"></script>
<script>
(function () {
    var taskId      = {{ $task->id }};
    var storeRoute  = '{{ route('subtasks.store', $task) }}';
    var reorderRoute = '{{ route('subtasks.reorder', $task) }}';

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    /* ── Add subtask ── */
    function buildRow(sub) {
        var li = document.createElement('li');
        li.className = 'subtask-item d-flex align-items-center gap-2 py-1 px-2 rounded mb-1';
        li.dataset.id = sub.id;
        li.style.cssText = 'background:var(--surface-2);border:1px solid var(--border);';

        li.innerHTML = [
            '<span class="subtask-drag" style="cursor:grab;color:var(--text-muted);padding:.1rem .2rem;">',
                '<i class="bi bi-grip-vertical"></i>',
            '</span>',
            '<input type="checkbox" class="subtask-toggle-check"',
                ' data-id="' + sub.id + '"',
                ' data-route="/subtasks/' + sub.id + '/toggle"',
                ' style="width:1.1rem;height:1.1rem;flex-shrink:0;accent-color:var(--purple-500);cursor:pointer;">',
            '<span class="subtask-title flex-grow-1" style="font-size:.9rem;color:var(--text);">',
                escHtml(sub.title),
            '</span>',
            '<button type="button" class="subtask-delete-btn btn btn-sm"',
                ' data-id="' + sub.id + '"',
                ' data-route="/subtasks/' + sub.id + '"',
                ' style="padding:.1rem .4rem;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#ef4444;border-radius:6px;line-height:1;">',
                '<i class="bi bi-x"></i>',
            '</button>',
        ].join('');

        wireRow(li);
        return li;
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function addSubtask(title) {
        fetch(storeRoute, {
            method: 'POST',
            body: JSON.stringify({ title: title }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'Accept': 'application/json',
            }
        })
        .then(function (r) {
            if (!r.ok) throw new Error();
            return r.json();
        })
        .then(function (sub) {
            var list = document.getElementById('subtask-list');
            list.appendChild(buildRow(sub));
            if (typeof window.toast === 'function') window.toast('Subtask added', 'success');
        })
        .catch(function () {
            if (typeof window.toast === 'function') window.toast('Could not add subtask', 'error');
        });
    }

    var input  = document.getElementById('new-subtask-input');
    var addBtn = document.getElementById('add-subtask-btn');

    if (addBtn) addBtn.addEventListener('click', function () {
        var val = input.value.trim();
        if (val) { addSubtask(val); input.value = ''; }
    });

    if (input) input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var val = this.value.trim();
            if (val) { addSubtask(val); this.value = ''; }
        }
    });

    /* ── Wire existing rows ── */
    function wireRow(li) {
        var checkbox = li.querySelector('.subtask-toggle-check');
        if (checkbox) {
            checkbox.addEventListener('change', function () {
                var route = this.dataset.route;
                var self  = this;
                self.disabled = true;
                fetch(route, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    self.checked = data.is_completed;
                    var titleEl = li.querySelector('.subtask-title');
                    if (titleEl) {
                        titleEl.style.textDecoration = data.is_completed ? 'line-through' : '';
                        titleEl.style.opacity        = data.is_completed ? '0.6' : '';
                    }
                })
                .catch(function () { self.checked = !self.checked; })
                .finally(function () { self.disabled = false; });
            });
        }

        var delBtn = li.querySelector('.subtask-delete-btn');
        if (delBtn) {
            delBtn.addEventListener('click', function () {
                var route = this.dataset.route;
                fetch(route, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' }
                })
                .then(function (r) {
                    if (!r.ok) throw new Error();
                    li.remove();
                })
                .catch(function () {
                    if (typeof window.toast === 'function') window.toast('Could not delete subtask', 'error');
                });
            });
        }
    }

    document.querySelectorAll('.subtask-item').forEach(wireRow);

    /* ── Drag-to-reorder (SortableJS) ── */
    var list = document.getElementById('subtask-list');
    if (list && typeof Sortable !== 'undefined') {
        Sortable.create(list, {
            handle: '.subtask-drag',
            animation: 150,
            onEnd: function () {
                var ids = Array.from(list.querySelectorAll('.subtask-item'))
                    .map(function (li) { return parseInt(li.dataset.id, 10); });

                fetch(reorderRoute, {
                    method: 'POST',
                    body: JSON.stringify({ ids: ids }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                        'Accept': 'application/json',
                    }
                });
            }
        });
    }
})();
</script>
@endpush
