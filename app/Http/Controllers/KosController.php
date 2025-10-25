<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosController extends Controller
{
    public function index()
    {
        $kos = Kos::with(['facilities', 'images', 'user:id,name'])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'List semua kos',
            'data' => $kos
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can add kos'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'price_per_month' => 'required|integer',
            'gender' => 'required|in:male,female,all'
        ]);

        $kos = Kos::create([
            'user_id' => $user->id,
            ...$validated
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kos created successfully',
            'data' => $kos
        ], 201);
    }

    public function show($id)
    {
        $kos = Kos::with(['facilities', 'images', 'user:id,name'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail kos ditemukan',
            'data' => $kos
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can edit kos'], 403);
        }

        $kos = Kos::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $kos->update($request->only('name', 'address', 'price_per_month', 'gender'));

        return response()->json([
            'status' => 'success',
            'message' => 'Kos updated successfully',
            'data' => $kos
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can delete kos'], 403);
        }

        $kos = Kos::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $kos->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kos deleted successfully'
        ], 200);
    }
}
