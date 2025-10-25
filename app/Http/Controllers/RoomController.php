<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index($kos_id)
    {
        $rooms = Room::where('kos_id', $kos_id)->get();
        return response()->json($rooms);
    }

    public function store(Request $request, $kos_id)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can add room'], 403);
        }

        $kos = Kos::where('id', $kos_id)->where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room = Room::create([
            'kos_id' => $kos->id,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Room created successfully', 'data' => $room], 201);
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can update room'], 403);
        }

        $room = Room::findOrFail($id);
        $kos = $room->kos;

        if ($kos->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized, this kos is not yours'], 403);
        }

        $room->update($request->only('name', 'status'));

        return response()->json(['message' => 'Room updated successfully', 'data' => $room]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized, only owner can delete room'], 403);
        }

        $room = Room::findOrFail($id);
        $kos = $room->kos;

        if ($kos->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized, this kos is not yours'], 403);
        }

        $room->delete();

        return response()->json(['message' => 'Room deleted successfully']);
    }
}
