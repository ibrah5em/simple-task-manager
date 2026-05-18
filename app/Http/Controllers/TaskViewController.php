<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskViewController extends Controller
{
    public function today(Request $request): View
    {
        $tasks = $request->user()->tasks()
            ->with('categories')
            ->withCount([
                'subtasks',
                'subtasks as completed_subtasks_count' => fn($q) => $q->where('is_completed', true),
            ])
            ->where('is_completed', false)
            ->whereDate('due_date', '<=', today())
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->get();

        $todayCount = $tasks->count();

        return view('today', compact('tasks', 'todayCount'));
    }

    public function inbox(Request $request): View
    {
        $tasks = $request->user()->tasks()
            ->with('categories')
            ->withCount([
                'subtasks',
                'subtasks as completed_subtasks_count' => fn($q) => $q->where('is_completed', true),
            ])
            ->where('is_completed', false)
            ->whereNull('due_date')
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $inboxCount = $tasks->count();

        return view('inbox', compact('tasks', 'inboxCount'));
    }

    public function calendar(): View
    {
        return view('calendar');
    }

    public function upcoming(Request $request): View
    {
        $tasks = $request->user()->tasks()
            ->with('categories')
            ->withCount([
                'subtasks',
                'subtasks as completed_subtasks_count' => fn($q) => $q->where('is_completed', true),
            ])
            ->where('is_completed', false)
            ->whereDate('due_date', '>', today())
            ->whereDate('due_date', '<=', today()->addDays(7))
            ->orderBy('due_date')
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->get();

        // Group by due_date for the "grouped by day" display
        $grouped = $tasks->groupBy(fn($t) => $t->due_date->format('Y-m-d'));

        return view('upcoming', compact('tasks', 'grouped'));
    }
}
