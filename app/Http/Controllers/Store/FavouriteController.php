<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer']
        ]);

        $userId    = Auth::id();
        $productId = (int)$request->product_id;

        $fav = Favourite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($fav) {
            $fav->delete();
            return response()->json(['status' => 'removed']);
        }

        Favourite::create([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);

        return response()->json(['status' => 'added']);
    }
}
