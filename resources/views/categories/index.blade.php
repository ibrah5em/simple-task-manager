@extends('layouts.app')

@section('content')

<style>
    .color-swatch {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .category-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .category-row:last-child {
        border-bottom: none;
    }
    .category-row:hover {
        background: rgba(139,92,246,.05);
    }
    .cat-name {
        font-weight: 600;
        font-size: .95rem;
        color: var(--text);
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cat-count-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .75rem;
        font-weight: 600;
        padding: .25rem .7rem;
        border-radius: 50px;
        background: rgba(139,92,246,.12);
        color: var(--purple-600);
        white-space: nowrap;
        flex-shrink: 0;
    }
    [data-bs-theme="dark"] .cat-count-badge {
        background: rgba(139,92,246,.2);
        color: var(--purple-400);
    }
    .cat-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--surface-2);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .9rem;
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s;
        flex-shrink: 0;
        padding: 0;
    }
    .cat-action-btn:hover {
        background: rgba(139,92,246,.12);
        color: var(--purple-600);
        border-color: rgba(139,92,246,.3);
    }
    .cat-delete-btn:hover {
        background: rgba(239,68,68,.12) !important;
        color: #ef4444 !important;
        border-color: rgba(239,68,68,.3) !important;
    }
    /* Color picker wrapper */
    .color-input-wrap {
        position: relative;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
    }
    .color-input-wrap input[type="color"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 2px solid var(--border) !important;
        border-radius: 10px !important;
        padding: 3px !important;
        cursor: pointer;
        background: transparent !important;
    }
    /* Collapsible create form */
    #createFormCollapse {
        overflow: hidden;
    }
</style>

{{-- Page header --}}
<div class="d-flex align-items-center justify-content-between mb-4 anim-fade-up flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--text)">Categories</h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:.875rem">
            {{ $categories->count() }} categor{{ $categories->count() !== 1 ? 'ies' : 'y' }} total
        </p>
    </div>
    <button type="button"
            class="btn-purple btn d-flex align-items-center gap-2"
            data-bs-toggle="collapse"
            data-bs-target="#createFormCollapse"
            aria-expanded="{{ ($errors->any() || $categories->isEmpty()) ? 'true' : 'false' }}"
            aria-controls="createFormCollapse">
        <i class="bi bi-plus-lg"></i> New Category
    </button>
</div>

{{-- Inline create form --}}
<div class="collapse anim-fade-up anim-delay-1 {{ ($errors->any() || $categories->isEmpty()) ? 'show' : '' }}" id="createFormCollapse">
    <div class="glass-card p-4 mb-4">
        <h6 class="fw-semibold mb-3" style="color:var(--text)">
            <i class="bi bi-plus-circle me-1" style="color:var(--purple-500);"></i> Add New Category
        </h6>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm">
                    <label for="new_cat_name" class="form-label">Name <span style="color:#ef4444">*</span></label>
                    <div style="position:relative;">
                        <i class="bi bi-tag" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;"></i>
                        <input id="new_cat_name"
                               type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               style="padding-left:2.4rem!important;"
                               placeholder="e.g. Work, Personal, Urgent…"
                               value="{{ old('name') }}"
                               maxlength="255"
                               required>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-auto">
                    <label class="form-label">Color</label>
                    <div class="color-input-wrap">
                        <input type="color" name="color" value="{{ old('color', '#8b5cf6') }}" title="Pick a color">
                    </div>
                    @error('color')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn-purple btn d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Add Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Categories list --}}
@if($categories->isEmpty())
    <div class="glass-card p-5 text-center anim-fade-up anim-delay-2">
        <i class="bi bi-tags" style="font-size:3rem;color:var(--purple-400);opacity:.6;"></i>
        <p class="mt-3 mb-2 fw-semibold" style="color:var(--text)">No categories yet</p>
        <p style="color:var(--text-muted);font-size:.875rem">
            Create your first category above to start organising your tasks.
        </p>
    </div>
@else
    <div class="glass-card anim-fade-up anim-delay-2" style="overflow:hidden;">
        @foreach($categories as $category)
        <div class="category-row">
            {{-- Color swatch --}}
            <span class="color-swatch" style="background:{{ $category->color }};"></span>

            {{-- Name --}}
            <span class="cat-name">{{ $category->name }}</span>

            {{-- Task count --}}
            <span class="cat-count-badge">
                <i class="bi bi-list-task"></i>
                {{ $category->tasks_count }} task{{ $category->tasks_count !== 1 ? 's' : '' }}
            </span>

            {{-- Edit button --}}
            <button type="button"
                    class="cat-action-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#editCatModal"
                    data-id="{{ $category->id }}"
                    data-name="{{ $category->name }}"
                    data-color="{{ $category->color }}"
                    data-route="{{ route('categories.update', $category) }}"
                    title="Edit category">
                <i class="bi bi-pencil"></i>
            </button>

            {{-- Delete button --}}
            <button type="button"
                    class="cat-action-btn cat-delete-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteCatModal"
                    data-id="{{ $category->id }}"
                    data-name="{{ $category->name }}"
                    data-route="{{ route('categories.destroy', $category) }}"
                    title="Delete category">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        @endforeach
    </div>
@endif

{{-- ── Edit Category Modal ── --}}
<div class="modal fade" id="editCatModal" tabindex="-1" aria-labelledby="editCatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-1">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:40px;height:40px;border-radius:50%;background:rgba(139,92,246,.15);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-pencil-fill" style="color:var(--purple-500);font-size:1rem;"></i>
                    </span>
                    <h5 class="modal-title fw-bold mb-0" id="editCatModalLabel" style="color:var(--text)">
                        Edit Category
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="filter:none;opacity:.6;"></button>
            </div>

            <form id="editCatForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body pt-3">

                    @error('edit_name')
                        <div class="alert alert-danger py-2 mb-3" style="border-radius:12px;font-size:.875rem;">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="mb-3">
                        <label for="edit_cat_name" class="form-label">Name <span style="color:#ef4444">*</span></label>
                        <div style="position:relative;">
                            <i class="bi bi-tag" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;"></i>
                            <input id="edit_cat_name"
                                   type="text"
                                   name="name"
                                   class="form-control"
                                   style="padding-left:2.4rem!important;"
                                   placeholder="Category name"
                                   maxlength="255"
                                   required>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Color</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="color-input-wrap">
                                <input id="edit_cat_color" type="color" name="color" value="#8b5cf6">
                            </div>
                            <span style="font-size:.83rem;color:var(--text-muted);">
                                Click to pick a colour for this category
                            </span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ── Delete Category Modal ── --}}
<div class="modal fade" id="deleteCatModal" tabindex="-1" aria-labelledby="deleteCatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-1">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:40px;height:40px;border-radius:50%;background:rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:1.1rem;"></i>
                    </span>
                    <h5 class="modal-title fw-bold mb-0" id="deleteCatModalLabel" style="color:var(--text)">
                        Delete Category
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="filter:none;opacity:.6;"></button>
            </div>
            <div class="modal-body">
                <p style="color:var(--text-muted);">
                    Are you sure you want to delete
                    <strong id="deleteCatName" style="color:var(--text);"></strong>?
                </p>
                <div class="glass-card-inner p-3 d-flex gap-2" style="font-size:.85rem;color:var(--text-muted);">
                    <i class="bi bi-info-circle-fill" style="color:var(--purple-400);flex-shrink:0;margin-top:.1rem;"></i>
                    <span>
                        Tasks linked to this category will <strong>not</strong> be deleted —
                        they will simply be unlinked from the category.
                    </span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCatForm" method="POST" action="" style="display:inline;">
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
    /* ── Edit modal population ── */
    var editModal = document.getElementById('editCatModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            var btn   = event.relatedTarget;
            var id    = btn.getAttribute('data-id');
            var name  = btn.getAttribute('data-name');
            var color = btn.getAttribute('data-color');
            var route = btn.getAttribute('data-route');

            document.getElementById('edit_cat_name').value  = name;
            document.getElementById('edit_cat_color').value = color || '#8b5cf6';
            document.getElementById('editCatForm').action   = route;
        });
    }

    /* ── Delete modal population ── */
    var deleteModal = document.getElementById('deleteCatModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var btn   = event.relatedTarget;
            var name  = btn.getAttribute('data-name');
            var route = btn.getAttribute('data-route');

            var nameEl = document.getElementById('deleteCatName');
            if (nameEl) nameEl.textContent = name;

            document.getElementById('deleteCatForm').action = route;
        });
    }
})();
</script>
@endpush
