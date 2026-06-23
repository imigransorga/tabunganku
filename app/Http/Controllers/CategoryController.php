<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()->categories()->orderBy('type')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
        $request->user()->categories()->create($data);

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($category->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
        $category->update($data);

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Request $request, Category $category)
    {
        abort_unless($category->user_id === $request->user()->id, 403);
        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
