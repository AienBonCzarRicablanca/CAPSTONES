<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use App\Models\LibraryItem;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryItemController extends Controller
{
    public function index(Request $request)
    {
        $items = LibraryItem::query()
            ->with('category')
            ->latest()
            ->paginate(20);

        return view('admin.library.items.index', compact('items'));
    }

    public function create()
    {
        $categories = LibraryCategory::orderBy('name')->get();
        return view('admin.library.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'library_category_id' => ['nullable', 'exists:library_categories,id'],
            'language' => ['required', 'string', 'max:20'],
            'difficulty' => ['required', 'string', 'max:20'],
            'text_content' => ['nullable', 'string'],
            'audio' => ['nullable', 'file', 'max:51200'],
        ]);

        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('library-audio', 'public');
        }

        $item = LibraryItem::create([
            'created_by' => $request->user()->id,
            'library_category_id' => $validated['library_category_id'] ?? null,
            'title' => $validated['title'],
            'language' => $validated['language'],
            'difficulty' => $validated['difficulty'],
            'text_content' => $validated['text_content'] ?? null,
            'audio_path' => $audioPath,
        ]);

        Activity::log($request->user(), 'admin.library_item.created', ['id' => $item->id]);

        return redirect()->route('admin.library.items.index');
    }

    public function edit(LibraryItem $item)
    {
        $categories = LibraryCategory::orderBy('name')->get();
        return view('admin.library.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, LibraryItem $item)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'library_category_id' => ['nullable', 'exists:library_categories,id'],
            'language' => ['required', 'string', 'max:20'],
            'difficulty' => ['required', 'string', 'max:20'],
            'text_content' => ['nullable', 'string'],
            'audio' => ['nullable', 'file', 'max:51200'],
            'remove_audio' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_audio') && $item->audio_path) {
            Storage::disk('public')->delete($item->audio_path);
            $item->audio_path = null;
        }
        if ($request->hasFile('audio')) {
            if ($item->audio_path) {
                Storage::disk('public')->delete($item->audio_path);
            }
            $item->audio_path = $request->file('audio')->store('library-audio', 'public');
        }

        $item->title = $validated['title'];
        $item->library_category_id = $validated['library_category_id'] ?? null;
        $item->language = $validated['language'];
        $item->difficulty = $validated['difficulty'];
        $item->text_content = $validated['text_content'] ?? null;
        $item->save();

        Activity::log($request->user(), 'admin.library_item.updated', ['id' => $item->id]);

        return redirect()->route('admin.library.items.index');
    }

    public function destroy(Request $request, LibraryItem $item)
    {
        $id = $item->id;
        if ($item->audio_path) {
            Storage::disk('public')->delete($item->audio_path);
        }
        $item->delete();

        Activity::log($request->user(), 'admin.library_item.deleted', ['id' => $id]);

        return back();
    }
}
