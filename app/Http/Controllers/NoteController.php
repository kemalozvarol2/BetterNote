<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard', ['notes' => auth()->user()->notes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        auth()->user()->notes()->create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => Str::slug($request->title)
        ]);

        return back()->with(['success' => 'Note saved.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return view('dashboard', ['notes' => auth()->user()->notes, 'selected_note' => $note]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $note->update([
            'title' => $request->title,
            'body' => $request->body
        ]);

        return back()->with(['success' => 'Note updated.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return to_route('notes.index')->with(['success' => 'Note deleted.']);
    }
}
