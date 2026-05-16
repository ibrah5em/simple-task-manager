<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = $request->user()->categories()->withCount('tasks')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $request->user()->categories()->create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        $category->tasks()->detach(); // detach only, do not delete tasks
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
