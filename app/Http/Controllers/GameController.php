<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $games = Game::all();
        return response()->json($games);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'developer' => 'required|string|max:255',
            'tahun'     => 'required|integer|min:1950|max:2100',
            'rilis'     => 'required|date',
            'genre'     => 'required|string|max:100',
            'harga'     => 'required|numeric|min:0',
        ]);

        $game = Game::create($validated);

        return response()->json([
            'message' => 'Data game berhasil ditambahkan.',
            'data'    => $game,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game): JsonResponse
    {
        return response()->json($game);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game): JsonResponse
    {
        $validated = $request->validate([
            'nama'      => 'sometimes|required|string|max:255',
            'developer' => 'sometimes|required|string|max:255',
            'tahun'     => 'sometimes|required|integer|min:1950|max:2100',
            'rilis'     => 'sometimes|required|date',
            'genre'     => 'sometimes|required|string|max:100',
            'harga'     => 'sometimes|required|numeric|min:0',
        ]);

        $game->update($validated);

        return response()->json([
            'message' => 'Data game berhasil diperbarui.',
            'data'    => $game,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game): JsonResponse
    {
        $game->delete();

        return response()->json([
            'message' => 'Data game berhasil dihapus.',
        ]);
    }
}
