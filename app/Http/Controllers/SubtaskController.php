<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $request->validate(['title' => 'required|string|max:255']);

        $position = $task->subtasks()->max('position') + 1;

        $subtask = $task->subtasks()->create([
            'title'        => $request->title,
            'is_completed' => false,
            'position'     => $position,
        ]);

        return response()->json([
            'id'           => $subtask->id,
            'title'        => $subtask->title,
            'is_completed' => $subtask->is_completed,
            'position'     => $subtask->position,
        ], 201);
    }

    public function toggle(Subtask $subtask): JsonResponse
    {
        $this->authorize('update', $subtask->task);

        $subtask->update(['is_completed' => !$subtask->is_completed]);

        return response()->json(['is_completed' => $subtask->is_completed]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        $this->authorize('update', $subtask->task);

        $subtask->delete();

        return response()->json(['ok' => true]);
    }

    public function reorder(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $ownedIds = $task->subtasks()->pluck('id')->toArray();

        foreach ($request->ids as $position => $id) {
            if (in_array($id, $ownedIds)) {
                Subtask::where('id', $id)->update(['position' => $position]);
            }
        }

        return response()->json(['ok' => true]);
    }
}
