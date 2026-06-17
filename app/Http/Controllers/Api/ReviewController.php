<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Public: get reviews for a service
    public function index($serviceId)
{
    $reviews = Review::with('tourist:id,first_name,last_name,avatar')
        ->where('service_id', $serviceId)
        ->where('is_hidden', false)
        ->latest()
        ->paginate(10);

    return response()->json($reviews);
}

    // Tourist: submit a review
    public function store(Request $request)
{
    $request->validate([
        'service_id' => 'required|exists:services,id',
        'booking_id' => 'required|exists:bookings,id',
        'rating'     => 'required|integer|min:1|max:5',
        'comment'    => 'nullable|string',
    ]);

    $booking = \App\Models\Booking::where('id', $request->booking_id)
        ->where('tourist_id', $request->user()->id)
        ->first();

    if (!$booking) {
        return response()->json(['message' => 'Booking not found.'], 404);
    }

    if ($booking->status !== 'completed') {
        return response()->json(['message' => 'You can only review completed bookings.'], 422);
    }

    $exists = Review::where('tourist_id', $request->user()->id)
        ->where('booking_id', $request->booking_id)
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'You have already reviewed this booking.'], 422);
    }

    $review = Review::create([
        'tourist_id' => $request->user()->id,
        'service_id' => $request->service_id,
        'booking_id' => $request->booking_id,
        'rating'     => $request->rating,
        'comment'    => $request->comment,
    ]);

    $avg = Review::where('service_id', $request->service_id)
        ->where('is_hidden', false)
        ->avg('rating');
    Service::where('id', $request->service_id)->update(['rating_avg' => $avg]);

    return response()->json([
        'message' => 'Review submitted successfully.',
        'review'  => $review,
    ], 201);
}
}