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
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label mb-0">Description <span style="font-size:.75rem;color:var(--text-muted);font-weight:400;">Markdown supported</span></label>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn md-tab-btn active" data-tab="write"
                                    style="font-size:.78rem;padding:.2rem .7rem;">Write</button>
                            <button type="button" class="btn md-tab-btn" data-tab="preview"
                                    style="font-size:.78rem;padding:.2rem .7rem;">Preview</button>
                        </div>
                    </div>
                    <div id="md-write-pane">
                        <textarea id="description" name="description" rows="5"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Supports **bold**, _italic_, - lists, `code`…">{{ old('description') }}</textarea>
                    </div>
                    <div id="md-preview-pane" class="d-none form-control"
                         style="min-height:130px;background:var(--surface-2);overflow-y:auto;font-size:.92rem;line-height:1.65;">
                    </div>
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

                <div class="mb-4">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                        <option value="medium" {{ old('priority','medium')==='medium'?'selected':'' }}>Medium</option>
                        <option value="high"   {{ old('priority')==='high'?'selected':'' }}>High</option>
                        <option value="low"    {{ old('priority')==='low'?'selected':'' }}>Low</option>
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Repeats</label>
                    <select name="recurrence" class="form-select @error('recurrence') is-invalid @enderror">
                        <option value="">Never</option>
                        <option value="daily"    {{ old('recurrence')==='daily'    ? 'selected' : '' }}>Daily</option>
                        <option value="weekdays" {{ old('recurrence')==='weekdays' ? 'selected' : '' }}>Weekdays (Mon–Fri)</option>
                        <option value="weekly"   {{ old('recurrence')==='weekly'   ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly"  {{ old('recurrence')==='monthly'  ? 'selected' : '' }}>Monthly</option>
                    </select>
                    <div class="form-text" style="font-size:.78rem;">Weekly/Monthly use the due date above to determine the day.</div>
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
                                   {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
                                   class="form-check-input cat-pill-check" style="margin-top:0;">
                            <span style="background:{{ $cat->color }};width:10px;height:10px;border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                            {{ $cat->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
<script>
(function () {
    var writePaneEl   = document.getElementById('md-write-pane');
    var previewPaneEl = document.getElementById('md-preview-pane');
    var textarea      = document.getElementById('description');

    document.querySelectorAll('.md-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.md-tab-btn').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            if (this.dataset.tab === 'preview') {
                var raw = textarea ? textarea.value : '';
                previewPaneEl.innerHTML = typeof marked !== 'undefined'
                    ? marked.parse(raw || '_Nothing to preview yet._')
                    : '<em>Preview unavailable.</em>';
                writePaneEl.classList.add('d-none');
                previewPaneEl.classList.remove('d-none');
            } else {
                previewPaneEl.classList.add('d-none');
                writePaneEl.classList.remove('d-none');
                if (textarea) textarea.focus();
            }
        });
    });
})();
</script>
@endpush
