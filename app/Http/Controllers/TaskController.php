<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = $request->user()->tasks()->latest()->paginate(15);
        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);

        $request->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'due_date'     => 'nullable|date',
            'is_completed' => 'boolean',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function toggle(Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        $task->update(['is_completed' => !$task->is_completed]);
        return response()->json(['is_completed' => $task->is_completed]);
    }

    public function bulk(Request $request, string $action): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        // Always filter by authenticated user — never trust client-supplied ids alone
        $tasks = Task::whereIn('id', $request->ids)
            ->where('user_id', $request->user()->id)
            ->get();

        match ($action) {
            'complete'   => $tasks->each(fn($t) => $t->update(['is_completed' => true])),
            'incomplete' => $tasks->each(fn($t) => $t->update(['is_completed' => false])),
            'delete'     => $tasks->each(fn($t) => $t->delete()),
            default      => abort(422, 'Unknown bulk action'),
        };

        return response()->json(['affected' => $tasks->count()]);
    }
}
