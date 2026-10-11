<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::all();
        return view('games.index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('games.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'developer' => 'required|string|max:255',
            'tahun' => 'required|integer|min:1950|max:2100',
            'rilis' => 'required|date',
            'genre' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
        ]);

        Game::create($request->only([
            'nama',
            'developer',
            'tahun',
            'rilis',
            'genre',
            'harga',
        ]));

        return redirect()
            ->route('games.index')
            ->with('success', 'Data game berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('games.show', compact('game'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('games.edit', compact('game'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $request->validate([
            'nama' => 'required|string|max:255',
            'developer' => 'required|string|max:255',
            'tahun' => 'required|integer|min:1950|max:2100',
            'rilis' => 'required|date',
            'genre' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
        ]);

        $game->update($request->only([
            'nama',
            'developer',
            'tahun',
            'rilis',
            'genre',
            'harga',
        ]));

        return redirect()
            ->route('games.index')
            ->with('success', 'Data game berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $game->delete();

        return redirect()
            ->route('games.index')
            ->with('success', 'Data game berhasil dihapus.');
    }
}
