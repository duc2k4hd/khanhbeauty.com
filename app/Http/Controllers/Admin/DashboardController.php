<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Portfolio;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'services' => Service::count(),
            'portfolios' => Portfolio::count(),
            'bookings' => 12, // Số mockup vì module booking đang trong Phase 2
            'visitors' => 2450 // Mockup
        ];
        return view('admin.dashboard.index', compact('stats'));
    }
}

