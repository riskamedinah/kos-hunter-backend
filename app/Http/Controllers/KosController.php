<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Hanya owner yang bisa melihat list kos mereka
        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, only owner can view kos list'
            ], 403);
        }

        $kos = Kos::with(['facilities', 'images', 'user:id,name'])
                 ->where('user_id', $user->id)
                 ->get();

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
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, only owner can add kos'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'price_per_month' => 'required|integer',
            'gender' => 'required|in:male,female,all',
            'facilities' => 'array',
            'facilities.*' => 'string',
            'images' => 'array',
            'images.*' => 'string'
        ]);

        $kos = Kos::create([
            'user_id' => $user->id,
            ...collect($validated)->except(['facilities', 'images'])->toArray()
        ]);

        if (!empty($validated['facilities'])) {
            $kos->facilities()->createMany(
                collect($validated['facilities'])->map(fn($f) => ['facility' => $f])->toArray()
            );
        }

        if (!empty($validated['images'])) {
            $kos->images()->createMany(
                collect($validated['images'])->map(fn($i) => ['file' => $i])->toArray()
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kos created successfully',
            'data' => $kos->load(['facilities', 'images', 'user:id,name'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, only owner can edit kos'
            ], 403);
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
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, only owner can delete kos'
            ], 403);
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

    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user->isOwner()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized, only owner can view kos details'
                ], 403);
            }

            $kos = Kos::with(['facilities', 'images', 'user:id,name'])
                     ->where('id', $id)
                     ->where('user_id', $user->id)
                     ->first();

            if (!$kos) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kos not found or you dont have access'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kos details fetched successfully',
                'data' => $kos
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error in KosController show method: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch kos details'
            ], 500);
        }
    }

    public function listForSociety(Request $request)
    {
        $gender = $request->query('gender');

        $query = Kos::with(['images' => function ($q) {
            $q->orderBy('created_at', 'asc')->limit(1);
        }, 'user:id,name'])
        ->when($gender, function($q) use ($gender) {
            return $q->where('gender', $gender);
        })
        ->orderBy('created_at', 'desc');

        $kos = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'List kos fetched successfully',
            'data' => $kos
        ], 200);
    }
}