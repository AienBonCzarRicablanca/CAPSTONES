<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use App\Support\Activity;
use Illuminate\Http\Request;

class LibraryCategoryController extends Controller
{
    public function index()
    {
        $categories = LibraryCategory::query()->orderBy('name')->paginate(20);
        return view('admin.library.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.library.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category = LibraryCategory::create(['name' => $validated['name']]);
        Activity::log($request->user(), 'admin.library_category.created', ['id' => $category->id]);

        return redirect()->route('admin.library.categories.index');
    }

    public function edit(LibraryCategory $category)
    {
        return view('admin.library.categories.edit', compact('category'));
    }

    public function update(Request $request, LibraryCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category->update(['name' => $validated['name']]);
        Activity::log($request->user(), 'admin.library_category.updated', ['id' => $category->id]);

        return redirect()->route('admin.library.categories.index');
    }

    public function destroy(Request $request, LibraryCategory $category)
    {
        $id = $category->id;
        $category->delete();
        Activity::log($request->user(), 'admin.library_category.deleted', ['id' => $id]);
        return back();
    }
}
