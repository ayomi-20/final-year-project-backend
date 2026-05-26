<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Tourist: create a booking
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'persons'    => 'required|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        $booking = Booking::create([
            'tourist_id' => $request->user()->id,
            'service_id' => $request->service_id,
            'persons'    => $request->persons,
            'notes'      => $request->notes,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => 'Booking created successfully.',
            'booking' => $booking->load('service'),
        ], 201);
    }

    // Tourist: get my bookings
    public function index(Request $request)
    {
        $bookings = Booking::with(['service.category', 'service.region', 'service.provider'])
            ->where('tourist_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    // Tourist: cancel a booking
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('tourist_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'This booking cannot be cancelled.'], 422);
        }

        $booking->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Booking cancelled successfully.']);
    }

    // Provider: update booking status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $provider = $request->user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found.'], 403);
        }

        $booking = Booking::whereHas('service', fn($q) =>
            $q->where('provider_id', $provider->id))
            ->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        $booking->update([
            'status' => $request->status,
            'cancellation_reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Booking status updated successfully.']);
    }
}