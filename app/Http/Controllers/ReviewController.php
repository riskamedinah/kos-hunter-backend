<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Lihat semua komentar di 1 kos
    public function index($kosId)
    {
        $reviews = Review::with(['user:id,name', 'replies.user:id,name'])
            ->where('kos_id', $kosId)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    // Tambah komentar (Society)
    public function store(Request $request, $kosId)
    {
        $user = Auth::user();

        $request->validate([
            'comment' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'kos_id' => $kosId,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $review
        ]);
    }

    // Balas komentar (Owner)
    public function reply(Request $request, $reviewId)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can reply'], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $parentReview = Review::findOrFail($reviewId);

        $reply = Review::create([
            'kos_id' => $parentReview->kos_id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'parent_id' => $parentReview->id,
        ]);

        return response()->json([
            'message' => 'Reply added successfully',
            'data' => $reply
        ]);
    }
}
