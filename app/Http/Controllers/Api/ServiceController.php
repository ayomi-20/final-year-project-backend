<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::with(['category', 'region', 'provider'])
            ->where('status', 'active')
            ->when($request->category, fn($q) =>
                $q->whereHas('category', fn($q) =>
                    $q->where('slug', $request->category)))
            ->when($request->region, fn($q) =>
                $q->whereHas('region', fn($q) =>
                    $q->where('name', $request->region)))
            ->when($request->search, fn($q) =>
                $q->where('title', 'like', "%{$request->search}%"))
            ->paginate(10);

        return response()->json($services);
    }

    public function show($slug)
    {
        $service = Service::with(['category', 'region', 'provider', 'reviews.tourist'])
            ->where('slug', $slug)
            ->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json(['service' => $service]);
    }
}