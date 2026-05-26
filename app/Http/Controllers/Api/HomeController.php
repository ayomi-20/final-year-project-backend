<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Region;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json([
            'categories' => Category::all(),
            'regions'    => Region::all(),

            'featured' => Service::where('is_featured', true)
                ->where('status', 'active')
                ->with(['category', 'region', 'provider'])
                ->take(6)
                ->get(),

            'top_experiences' => Service::where('status', 'active')
                ->with(['category', 'region', 'provider'])
                ->orderBy('rating_avg', 'desc')
                ->take(5)
                ->get(),

            'recent_reviews' => \App\Models\Review::with(['tourist:id,first_name,last_name', 'service:id,title,slug'])
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}