<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    // Tourist stats
    public function touristStats(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'total_bookings'     => Booking::where('tourist_id', $userId)->count(),
            'pending_bookings'   => Booking::where('tourist_id', $userId)->where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('tourist_id', $userId)->where('status', 'completed')->count(),
            'cancelled_bookings' => Booking::where('tourist_id', $userId)->where('status', 'cancelled')->count(),
        ]);
    }

    // Provider stats
    public function providerStats(Request $request)
    {
        $provider = $request->user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found.'], 404);
        }

        $serviceIds = Service::where('provider_id', $provider->id)->pluck('id');

        return response()->json([
            'total_services'     => $serviceIds->count(),
            'total_bookings'     => Booking::whereIn('service_id', $serviceIds)->count(),
            'pending_bookings'   => Booking::whereIn('service_id', $serviceIds)->where('status', 'pending')->count(),
            'completed_bookings' => Booking::whereIn('service_id', $serviceIds)->where('status', 'completed')->count(),
        ]);
    }

    // Admin stats
    public function adminStats(Request $request)
    {
        return response()->json([
            'total_users'     => User::where('role', 'tourist')->count(),
            'total_providers' => Provider::where('status', 'approved')->count(),
            'pending_providers' => Provider::where('status', 'pending')->count(),
            'total_bookings'  => Booking::count(),
            'total_services'  => Service::where('status', 'active')->count(),
        ]);
    }
}