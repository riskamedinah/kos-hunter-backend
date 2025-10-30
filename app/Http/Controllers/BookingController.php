<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Book::with(['kos:id,name,address,user_id', 'user:id,name'])
            ->orderBy('created_at', 'desc');

        if ($user->isSociety()) {
            $query->where('user_id', $user->id);
        }

        if ($user->isOwner()) {
            $query->whereHas('kos', fn($q) => $q->where('user_id', $user->id));
        }

        $bookings = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar booking berhasil diambil',
            'data' => $bookings
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isSociety()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya society yang dapat melakukan booking',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'kos_id' => 'required|exists:kos,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $book = Book::create([
            'kos_id' => $validated['kos_id'],
            'user_id' => $user->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking berhasil dibuat',
            'data' => $book
        ], 201);
    }

    public function print($id)
    {
        $user = Auth::user();
        $book = Book::with(['kos', 'user'])->findOrFail($id);

        if ($book->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat mencetak booking milik orang lain',
                'data' => null
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti booking berhasil diambil',
            'data' => [
                'booking_code' => 'BOOK-' . str_pad($book->id, 5, '0', STR_PAD_LEFT),
                'kos_name' => $book->kos->name,
                'address' => $book->kos->address,
                'start_date' => $book->start_date,
                'end_date' => $book->end_date,
                'status' => $book->status,
                'society_name' => $book->user->name,
                'created_at' => $book->created_at,
            ]
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya owner yang dapat mengubah status booking',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accept,reject'
        ]);

        $book = Book::with('kos')->findOrFail($id);

        if ($book->kos->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden, bukan kos milik Anda',
                'data' => null
            ], 403);
        }

        $book->update(['status' => $validated['status']]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status booking berhasil diperbarui',
            'data' => $book
        ], 200);
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya owner yang dapat melihat riwayat booking',
                'data' => null
            ], 403);
        }

        $date = $request->query('date');
        $month = $request->query('month');

        $query = Book::with(['user:id,name', 'kos:id,name,user_id'])
            ->whereHas('kos', fn($q) => $q->where('user_id', $user->id));

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat booking berhasil diambil',
            'data' => $bookings
        ], 200);
    }
}
