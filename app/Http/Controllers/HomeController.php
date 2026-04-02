<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Brand;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Chỉ gọi Cache tĩnh cho Toàn bộ Dữ liệu Trang chủ -> Load siêu tốc
        $data = Cache::remember('home_data', now()->addHours(12), function () {
            return [
                'categories' => ServiceCategory::with(['services' => function($q) {
                    $q->active()->with('featuredImage');
                }, 'services.variants' => function($q) {
                    $q->active();
                }])->withCount('services')->active()->get(),

                'featuredServices' => Service::with(['category', 'featuredImage'])
                    ->featured()
                    ->active()
                    ->take(6)
                    ->get(),

                'latestPortfolios' => Portfolio::with(['beforeImage', 'afterImage'])
                    ->latest()
                    ->take(6)
                    ->get(),

                'latestPosts' => Post::with(['category', 'featuredImage'])
                    ->published()
                    ->latest()
                    ->take(3)
                    ->get(),

                'brands' => Brand::active()->get(),
            ];
        });

        // Tách dữ liệu phân bổ View
        $categories = $data['categories'];
        $featuredServices = $data['featuredServices'];
        $latestPortfolios = $data['latestPortfolios'];
        $latestPosts = $data['latestPosts'];
        $brands = $data['brands'];

        // 2. SEO Configuration
        SEOTools::setTitle('Khánh Beauty — Dịch Vụ Trang Điểm Chuyên Nghiệp Tại Nhà');
        SEOTools::setDescription('Khám phá dịch vụ makeup chuyên nghiệp tại Khánh Beauty. Trang điểm dự tiệc, cô dâu, sự kiện với phong cách tự nhiên, sang trọng ngay tại không gian của bạn.');
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::twitter()->setSite('@khanhbeauty');

        return view('clients.pages.home.index', compact(
            'categories',
            'featuredServices',
            'latestPortfolios',
            'latestPosts',
            'brands'
        ));
    }
}
