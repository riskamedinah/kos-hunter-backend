<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // 📍 List bookings (Society hanya lihat miliknya)
    public function index()
    {
        $user = Auth::user();

        $bookings = Booking::with(['room.kos:id,name,address', 'society:id,name'])
            ->when($user->role === 'society', fn($q) => $q->where('society_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ], 200);
    }

    // 🧾 Create booking
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'society') {
            return response()->json(['message' => 'Only society can make a booking'], 403);
        }

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Hitung total harga berdasarkan durasi (hari)
        $days = (strtotime($validated['check_out_date']) - strtotime($validated['check_in_date'])) / 86400;
        $total = $days * $room->price_per_day;

        $booking = Booking::create([
            'society_id' => $user->id,
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_price' => $total,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    // ✏️ Update status booking (owner)
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json(['message' => 'Only owner can update booking status'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,completed,cancelled'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking status updated successfully',
            'data' => $booking
        ], 200);
    }

    // ❌ Delete booking (opsional)
    public function destroy($id)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($id);

        if ($booking->society_id !== $user->id && !$user->isOwner()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Booking deleted successfully'
        ], 200);
    }
}
