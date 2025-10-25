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
        return response()->json($facilities);
    }

    public function store(Request $request, $kosId)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'facility' => 'required|string|max:255'
        ]);

        $kos = Kos::where('id', $kosId)->where('user_id', $user->id)->firstOrFail();

        $facility = KosFacility::create([
            'kos_id' => $kos->id,
            'facility' => $request->facility,
        ]);

        return response()->json(['message' => 'Facility added successfully', 'data' => $facility], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $facility = KosFacility::findOrFail($id);
        if ($facility->kos->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'facility' => 'required|string|max:255'
        ]);

        $facility->update(['facility' => $request->facility]);

        return response()->json(['message' => 'Facility updated successfully', 'data' => $facility]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $facility = KosFacility::findOrFail($id);
        if ($facility->kos->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $facility->delete();
        return response()->json(['message' => 'Facility deleted successfully']);
    }
}
