<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = Review::with(['user:id,name', 'game.platforms:id,nama,pabrikan'])
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function availableGames(): JsonResponse
    {
        $takenIds = Review::where('user_id', Auth::id())->pluck('game_id');

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
                    $exists = Review::where('user_id', Auth::id())
                        ->where('game_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Kamu sudah membuat ulasan untuk game ini.');
                    }
                },
            ],
            'rating' => 'required|integer|min:1|max:5',
            'judul' => 'required|string|max:150',
            'isi' => 'required|string|max:5000',
            'status_main' => 'required|in:belum,sedang,tamat,dropped',
            'jam_main' => 'nullable|integer|min:0|max:100000',
        ]);

        $validated['user_id'] = Auth::id();

        $review = Review::create($validated);
        $review->load(['user:id,name', 'game.platforms:id,nama,pabrikan']);

        return response()->json([
            'message' => 'Ulasan berhasil dibuat.',
            'data' => $review,
        ], 201);
    }

    public function show(Review $review): JsonResponse
    {
        $review->load(['user:id,name', 'game.platforms:id,nama,pabrikan']);

        return response()->json($review);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        $this->authorizeOwnership($review);

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'judul' => 'sometimes|required|string|max:150',
            'isi' => 'sometimes|required|string|max:5000',
            'status_main' => 'sometimes|required|in:belum,sedang,tamat,dropped',
            'jam_main' => 'nullable|integer|min:0|max:100000',
        ]);

        $review->update($validated);
        $review->load(['user:id,name', 'game.platforms:id,nama,pabrikan']);

        return response()->json([
            'message' => 'Ulasan diperbarui.',
            'data' => $review,
        ]);
    }

    public function destroy(Review $review): JsonResponse
    {
        $this->authorizeOwnership($review);
        $review->delete();

        return response()->json([
            'message' => 'Ulasan dihapus.',
        ]);
    }

    protected function authorizeOwnership(Review $review): void
    {
        if ($review->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Kamu tidak punya akses ke ulasan ini.');
        }
    }
}
