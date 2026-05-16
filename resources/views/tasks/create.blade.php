@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <div class="d-flex align-items-center gap-3 mb-4 anim-fade-up">
            <a href="{{ route('tasks.index') }}" class="btn-ghost btn btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color:var(--text)">New Task</h4>
            </div>
        </div>

        <div class="glass-card p-4 anim-fade-up anim-delay-1">

            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

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
                               value="{{ old('title') }}" required maxlength="255">
                    </div>
                    @error('title')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Optional — add details about this task...">{{ old('description') }}</textarea>
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
                               value="{{ old('due_date') }}">
                    </div>
                    @error('due_date')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3 pt-2">
                    <button type="submit" class="btn-purple btn d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle"></i> Save Task
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
