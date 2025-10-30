<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index($kosId)
    {
        $reviews = Review::with(['user:id,name'])
            ->where('kos_id', $kosId)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'List review berhasil diambil',
            'data' => $reviews
        ], 200);
    }

    public function store(Request $request, $kosId)
    {
        $user = Auth::user();

        if (!$user->isSociety()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, hanya society yang bisa menambahkan review',
                'data' => null
            ], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $kos = Kos::findOrFail($kosId);

        $review = Review::create([
            'kos_id' => $kos->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review berhasil ditambahkan',
            'data' => $review
        ], 201);
    }

    public function reply(Request $request, $reviewId)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, hanya owner yang bisa membalas review',
                'data' => null
            ], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $parentReview = Review::findOrFail($reviewId);

        $reply = Review::create([
            'kos_id' => $parentReview->kos_id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Balasan berhasil dikirim',
            'data' => $reply
        ], 201);
    }
}
