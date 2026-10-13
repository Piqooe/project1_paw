<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Platform;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'games' => Game::count(),
            'platforms' => Platform::count(),
            'wishlists' => Wishlist::where('user_id', $userId)->count(),
            'reviews_mine' => Review::where('user_id', $userId)->count(),
            'reviews_total' => Review::count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
