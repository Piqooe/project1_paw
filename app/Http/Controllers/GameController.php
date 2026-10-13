<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\SupabaseStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct(
        protected SupabaseStorageService $supabaseStorage
    ) {
    }

    public function index(): JsonResponse
    {
        $games = Game::with('platforms:id,nama,pabrikan')->get();
        return response()->json($games);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'developer' => 'required|string|max:255',
            'tahun' => 'required|integer|min:1950|max:2100',
            'rilis' => 'required|date',
            'genre' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'gambar' => $request->hasFile('gambar')
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
                : 'nullable|string|max:2048',
            'platform_ids' => 'nullable|array',
            'platform_ids.*' => 'integer|exists:platforms,id',
        ]);

        if ($request->hasFile('gambar')) {
            try {
                $validated['gambar'] = $this->supabaseStorage->upload($request->file('gambar'));
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal mengunggah gambar ke Supabase Storage.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $platformIds = $validated['platform_ids'] ?? [];
        unset($validated['platform_ids']);

        $game = Game::create($validated);
        $game->platforms()->sync($platformIds);
        $game->load('platforms:id,nama,pabrikan');

        return response()->json([
            'message' => 'Data game berhasil ditambahkan.',
            'data' => $game,
        ], 201);
    }

    public function show(Game $game): JsonResponse
    {
        $game->load('platforms:id,nama,pabrikan');
        return response()->json($game);
    }

    public function update(Request $request, Game $game): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'developer' => 'sometimes|required|string|max:255',
            'tahun' => 'sometimes|required|integer|min:1950|max:2100',
            'rilis' => 'sometimes|required|date',
            'genre' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|numeric|min:0',
            'gambar' => $request->hasFile('gambar')
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
                : 'nullable|string|max:2048',
            'platform_ids' => 'nullable|array',
            'platform_ids.*' => 'integer|exists:platforms,id',
        ]);

        if ($request->hasFile('gambar')) {
            try {
                if ($game->gambar) {
                    $this->supabaseStorage->delete($game->gambar);
                }
                $validated['gambar'] = $this->supabaseStorage->upload($request->file('gambar'));
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal mengunggah gambar ke Supabase Storage.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $syncPlatforms = $request->exists('platform_ids');
        $platformIds = $validated['platform_ids'] ?? [];
        unset($validated['platform_ids']);

        $game->update($validated);

        if ($syncPlatforms) {
            $game->platforms()->sync($platformIds);
        }

        $game->load('platforms:id,nama,pabrikan');

        return response()->json([
            'message' => 'Data game berhasil diperbarui.',
            'data' => $game,
        ]);
    }

    public function destroy(Game $game): JsonResponse
    {
        if ($game->gambar) {
            $this->supabaseStorage->delete($game->gambar);
        }

        $game->delete();

        return response()->json([
            'message' => 'Data game berhasil dihapus.',
        ]);
    }
}
