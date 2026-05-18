<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\RecurrenceService;
use App\Services\TaskInputParser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status', 'category', 'sort']);

        $tasks = $request->user()->tasks()
            ->with('categories')
            ->withCount([
                'subtasks',
                'subtasks as completed_subtasks_count' => fn($q) => $q->where('is_completed', true),
            ])
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
            'recurrence'   => 'nullable|in:daily,weekdays,weekly,monthly',
            'categories'   => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        if (!empty($validated['recurrence'])) {
            $dueDate = isset($validated['due_date']) ? \Illuminate\Support\Carbon::parse($validated['due_date']) : null;
            $validated['recurrence_rule'] = app(RecurrenceService::class)
                ->presetToRrule($validated['recurrence'], $dueDate);
        }
        unset($validated['recurrence']);

        $task = $request->user()->tasks()->create($validated);

        if (!empty($validated['categories'])) {
            $ownedIds = $request->user()->categories()
                ->whereIn('id', $validated['categories'])->pluck('id');
            $task->categories()->sync($ownedIds);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function edit(Task $task, Request $request): View
    {
        $this->authorize('update', $task);
        $task->load('subtasks');
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
            'recurrence'   => 'nullable|in:never,daily,weekdays,weekly,monthly',
            'categories'   => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $recurrencePreset = $validated['recurrence'] ?? 'never';
        unset($validated['recurrence']);

        if ($recurrencePreset === 'never') {
            $validated['recurrence_rule'] = null;
        } else {
            $dueDate = isset($validated['due_date']) ? \Illuminate\Support\Carbon::parse($validated['due_date']) : $task->due_date;
            $validated['recurrence_rule'] = app(RecurrenceService::class)
                ->presetToRrule($recurrencePreset, $dueDate);
        }

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

        $newState = !$task->is_completed;
        $task->update(['is_completed' => $newState]);

        if ($newState) {
            $rootTask = $task->recurrence_rule
                ? $task
                : ($task->recurrence_parent_id ? $task->recurrenceParent : null);

            if ($rootTask && $rootTask->recurrence_rule) {
                $afterDate = $task->due_date ?? today();
                app(RecurrenceService::class)->spawnNextOccurrence($rootTask, $afterDate);
            }
        }

        return response()->json(['is_completed' => $task->is_completed]);
    }

    public function snooze(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        $request->validate(['until' => 'required|date|after:today']);
        $task->update(['due_date' => $request->until]);
        return response()->json(['due_date' => $task->due_date->format('M d, Y')]);
    }

    public function parse(Request $request): JsonResponse
    {
        $request->validate(['input' => 'required|string|max:500']);

        $categories = $request->user()->categories()->get(['id', 'name']);
        $parser     = new TaskInputParser();
        $result     = $parser->parse($request->input('input'), $categories);

        return response()->json($result);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $ownedIds = $request->user()->tasks()->pluck('id')->toArray();

        foreach ($request->ids as $position => $id) {
            if (in_array($id, $ownedIds)) {
                Task::where('id', $id)->update(['position' => $position]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function calendarFeed(Request $request): JsonResponse
    {
        $start = $request->query('start') ? Carbon::parse($request->query('start')) : now()->startOfMonth();
        $end   = $request->query('end')   ? Carbon::parse($request->query('end'))   : now()->endOfMonth();

        $priorityColors = ['high' => '#ef4444', 'medium' => '#f59e0b', 'low' => '#10b981'];

        $events = $request->user()->tasks()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->get(['id', 'title', 'due_date', 'is_completed', 'priority'])
            ->map(fn($t) => [
                'id'              => $t->id,
                'title'           => $t->title,
                'start'           => $t->due_date->toDateString(),
                'url'             => route('tasks.edit', $t),
                'backgroundColor' => $t->is_completed ? '#6b7280' : ($priorityColors[$t->priority] ?? '#8b5cf6'),
                'borderColor'     => $t->is_completed ? '#6b7280' : ($priorityColors[$t->priority] ?? '#8b5cf6'),
                'textColor'       => '#ffffff',
                'classNames'      => $t->is_completed ? ['fc-event-completed'] : [],
            ]);

        return response()->json($events);
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
