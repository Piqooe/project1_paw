<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Services\SupabaseStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function __construct(
        protected SupabaseStorageService $supabaseStorage
    ) {
    }

    public function index(): JsonResponse
    {
        $platforms = Platform::withCount('games')->orderBy('nama')->get();
        return response()->json($platforms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:platforms,nama',
            'pabrikan' => 'nullable|string|max:255',
            'tahun_rilis' => 'nullable|integer|min:1950|max:2100',
            'ikon' => $request->hasFile('ikon')
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048'
                : 'nullable|string|max:2048',
        ]);

        if ($request->hasFile('ikon')) {
            try {
                $validated['ikon'] = $this->supabaseStorage->upload($request->file('ikon'));
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal mengunggah ikon ke Supabase Storage.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $platform = Platform::create($validated);

        return response()->json([
            'message' => 'Platform berhasil ditambahkan.',
            'data' => $platform,
        ], 201);
    }

    public function show(Platform $platform): JsonResponse
    {
        $platform->load('games');
        return response()->json($platform);
    }

    public function update(Request $request, Platform $platform): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255|unique:platforms,nama,' . $platform->id,
            'pabrikan' => 'nullable|string|max:255',
            'tahun_rilis' => 'nullable|integer|min:1950|max:2100',
            'ikon' => $request->hasFile('ikon')
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048'
                : 'nullable|string|max:2048',
        ]);

        if ($request->hasFile('ikon')) {
            try {
                if ($platform->ikon) {
                    $this->supabaseStorage->delete($platform->ikon);
                }
                $validated['ikon'] = $this->supabaseStorage->upload($request->file('ikon'));
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal mengunggah ikon ke Supabase Storage.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } elseif ($request->exists('ikon') && $request->input('ikon') === '') {
            if ($platform->ikon) {
                $this->supabaseStorage->delete($platform->ikon);
            }
            $validated['ikon'] = null;
        }

        $platform->update($validated);

        return response()->json([
            'message' => 'Platform berhasil diperbarui.',
            'data' => $platform,
        ]);
    }

    public function destroy(Platform $platform): JsonResponse
    {
        if ($platform->ikon) {
            $this->supabaseStorage->delete($platform->ikon);
        }

        $platform->delete();

        return response()->json([
            'message' => 'Platform berhasil dihapus.',
        ]);
    }
}
