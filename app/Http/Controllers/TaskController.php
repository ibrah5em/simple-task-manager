<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskInputParser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status', 'category', 'sort']);

        $tasks = $request->user()->tasks()
            ->with('categories')
            ->filter($filters)
            ->sort($filters['sort'] ?? '')
            ->paginate(15)
            ->withQueryString();

        $categories = $request->user()->categories()->orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'categories', 'filters'));
    }

    public function create(Request $request): View
    {
        $categories = $request->user()->categories()->orderBy('name')->get();
        return view('tasks.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'due_date'     => 'nullable|date',
            'priority'     => 'in:low,medium,high',
            'categories'   => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $task = $request->user()->tasks()->create($validated);

        if (!empty($validated['categories'])) {
            // Only attach categories owned by this user
            $ownedIds = $request->user()->categories()
                ->whereIn('id', $validated['categories'])->pluck('id');
            $task->categories()->sync($ownedIds);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function edit(Task $task, Request $request): View
    {
        $this->authorize('update', $task);
        $categories = $request->user()->categories()->orderBy('name')->get();
        return view('tasks.edit', compact('task', 'categories'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'due_date'     => 'nullable|date',
            'is_completed' => 'boolean',
            'priority'     => 'in:low,medium,high',
            'categories'   => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $task->update($validated);

        $ownedIds = $request->user()->categories()
            ->whereIn('id', $validated['categories'] ?? [])->pluck('id');
        $task->categories()->sync($ownedIds);

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

    public function parse(Request $request): JsonResponse
    {
        $request->validate(['input' => 'required|string|max:500']);

        $categories = $request->user()->categories()->get(['id', 'name']);
        $parser     = new TaskInputParser();
        $result     = $parser->parse($request->input('input'), $categories);

        return response()->json($result);
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
