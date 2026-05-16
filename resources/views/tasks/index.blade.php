@extends('layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 anim-fade-up flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--text)">My Tasks</h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:.875rem">
            {{ $tasks->count() }} task{{ $tasks->count() !== 1 ? 's' : '' }} total
        </p>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn-purple btn d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Task
    </a>
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
                        <th class="d-none d-sm-table-cell">#</th>
                        <th>Title</th>
                        <th class="d-none d-md-table-cell">Description</th>
                        <th class="d-none d-sm-table-cell">Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $i => $task)
                    <tr class="anim-fade-up" style="animation-delay: {{ ($i * 0.06) + 0.1 }}s">
                        <td class="d-none d-sm-table-cell" style="color:var(--text-muted);font-size:.8rem;width:40px;">{{ $i + 1 }}</td>
                        <td>
                            <span class="fw-semibold" style="color:var(--text)">{{ $task->title }}</span>
                        </td>
                        <td class="d-none d-md-table-cell" style="color:var(--text-muted);font-size:.875rem;max-width:220px;">
                            <span class="text-truncate d-block">{{ $task->description ?? '-' }}</span>
                        </td>
                        <td class="d-none d-sm-table-cell" style="font-size:.875rem;color:var(--text-muted);white-space:nowrap;">
                            @if($task->due_date)
                                <i class="bi bi-calendar3 me-1" style="color:var(--purple-400)"></i>
                                {{ $task->due_date->format('M d, Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($task->is_completed)
                                <span class="badge-completed"><i class="bi bi-check2 me-1"></i>Done</span>
                            @else
                                <span class="badge-incomplete"><i class="bi bi-clock me-1"></i>Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
