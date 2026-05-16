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
