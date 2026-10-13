<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $wishlists = Wishlist::with('game.platforms:id,nama,pabrikan')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json($wishlists);
    }

    public function availableGames(): JsonResponse
    {
        $takenIds = Wishlist::where('user_id', Auth::id())->pluck('game_id');

        $games = Game::whereNotIn('id', $takenIds)
            ->orderBy('nama')
            ->get(['id', 'nama', 'developer', 'tahun', 'genre', 'harga', 'gambar']);

        return response()->json($games);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_id' => [
                'required',
                'integer',
                'exists:games,id',
                function ($attribute, $value, $fail) {
                    $exists = Wishlist::where('user_id', Auth::id())
                        ->where('game_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Game ini sudah ada di wishlistmu.');
                    }
                },
            ],
            'prioritas' => 'required|in:rendah,sedang,tinggi',
            'target_harga' => 'nullable|integer|min:0|max:2147483647',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();

        $wishlist = Wishlist::create($validated);
        $wishlist->load('game.platforms:id,nama,pabrikan');

        return response()->json([
            'message' => 'Game ditambahkan ke wishlist.',
            'data' => $wishlist,
        ], 201);
    }

    public function show(Wishlist $wishlist): JsonResponse
    {
        $this->authorizeOwnership($wishlist);
        $wishlist->load('game.platforms:id,nama,pabrikan');

        return response()->json($wishlist);
    }

    public function update(Request $request, Wishlist $wishlist): JsonResponse
    {
        $this->authorizeOwnership($wishlist);

        $validated = $request->validate([
            'prioritas' => 'sometimes|required|in:rendah,sedang,tinggi',
            'target_harga' => 'nullable|integer|min:0|max:2147483647',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $wishlist->update($validated);
        $wishlist->load('game.platforms:id,nama,pabrikan');

        return response()->json([
            'message' => 'Wishlist diperbarui.',
            'data' => $wishlist,
        ]);
    }

    public function destroy(Wishlist $wishlist): JsonResponse
    {
        $this->authorizeOwnership($wishlist);
        $wishlist->delete();

        return response()->json([
            'message' => 'Wishlist dihapus.',
        ]);
    }

    protected function authorizeOwnership(Wishlist $wishlist): void
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Kamu tidak punya akses ke wishlist ini.');
        }
    }
}
