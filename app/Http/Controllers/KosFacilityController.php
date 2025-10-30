<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\KosFacility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosFacilityController extends Controller
{
    public function index($kosId)
    {
        $facilities = KosFacility::where('kos_id', $kosId)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Facilities retrieved successfully',
            'data' => $facilities
        ], 200);
    }

    public function store(Request $request, $kosId)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $request->validate([
            'facilities' => 'required|array',
            'facilities.*' => 'required|string|max:255'
        ]);

        $kos = Kos::where('id', $kosId)->where('user_id', $user->id)->firstOrFail();

        $createdFacilities = [];
        foreach ($request->facilities as $f) {
            $facility = KosFacility::create([
                'kos_id' => $kos->id,
                'facility' => $f,
            ]);
            $createdFacilities[] = $facility;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Facilities added successfully',
            'data' => $createdFacilities
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $facility = KosFacility::findOrFail($id);
        if ($facility->kos->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden',
                'data' => null
            ], 403);
        }

        $request->validate([
            'facility' => 'required|string|max:255'
        ]);

        $facility->update(['facility' => $request->facility]);

        return response()->json([
            'status' => 'success',
            'message' => 'Facility updated successfully',
            'data' => $facility
        ], 201);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $facility = KosFacility::findOrFail($id);
        if ($facility->kos->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden',
                'data' => null
            ], 403);
        }

        $facility->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Facility deleted successfully',
            'data' => null
        ], 200);
    }
}
