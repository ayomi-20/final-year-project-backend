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
            'featured'   => Service::where('is_featured', true)
                                ->where('status', 'active')
                                ->with(['category', 'region', 'provider'])
                                ->take(6)
                                ->get(),
        ]);
    }
}