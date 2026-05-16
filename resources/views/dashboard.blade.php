@extends('layouts.app')

@section('content')
<div class="row g-4">

    {{-- Welcome Header --}}
    <div class="col-12 anim-fade-up">
        <div class="glass-card p-4">
            <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color:var(--text)">
                        Good day, <span style="color:var(--purple-500)">{{ auth()->user()->name }}</span>
                    </h4>
                    <p class="mb-0" style="color:var(--text-muted);font-size:.9rem">
                        Here's an overview of your tasks.
                    </p>
                </div>
                <a href="{{ route('tasks.create') }}" class="btn-purple btn d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> New Task
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="col-md-4 anim-fade-up anim-delay-1">
        <div class="stat-bubble h-100">
            <div style="font-size:2.5rem;font-weight:800;line-height:1;">{{ $taskCount }}</div>
            <div style="opacity:.8;margin-top:.25rem;font-size:.9rem;">Total Tasks</div>
            <i class="bi bi-list-check" style="position:absolute;bottom:1rem;right:1.25rem;font-size:2rem;opacity:.2;"></i>
        </div>
    </div>

    <div class="col-md-4 anim-fade-up anim-delay-2">
        <div class="glass-card h-100 p-4 d-flex flex-column justify-content-between">
            <div style="color:var(--text-muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;">
                Quick Actions
            </div>
            <div class="d-grid gap-2 mt-3">
                <a href="{{ route('tasks.create') }}" class="btn-purple btn d-flex align-items-center gap-2 justify-content-center">
                    <i class="bi bi-plus-circle"></i> Create Task
                </a>
                <a href="{{ route('tasks.index') }}" class="btn-ghost btn d-flex align-items-center gap-2 justify-content-center">
                    <i class="bi bi-eye"></i> View All Tasks
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 anim-fade-up anim-delay-3">
        <div class="glass-card h-100 p-4">
            <div style="color:var(--text-muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.75rem;">
                Account Info
            </div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--purple-600),var(--purple-400));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold" style="color:var(--text);font-size:.9rem;">{{ auth()->user()->name }}</div>
                    <div style="color:var(--text-muted);font-size:.8rem;">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <span class="badge-{{ auth()->user()->role === 'admin' ? 'completed' : 'incomplete' }}" style="font-size:.72rem;">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
    </div>

</div>
@endsection
