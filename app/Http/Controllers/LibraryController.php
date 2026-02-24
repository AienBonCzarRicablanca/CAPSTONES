<?php

namespace App\Http\Controllers;

use App\Models\LibraryCategory;
use App\Models\LibraryItem;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryItem::query()->with('category');

        if ($request->filled('language')) {
            $query->where('language', $request->string('language'));
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->string('difficulty'));
        }

        if ($request->filled('category')) {
            $query->where('library_category_id', $request->integer('category'));
        }

        $items = $query->latest()->paginate(12)->withQueryString();
        $categories = LibraryCategory::orderBy('name')->get();

        return view('library.index', compact('items', 'categories'));
    }

    public function show(LibraryItem $item)
    {
        $item->load('category');
        return view('library.show', compact('item'));
    }
}
