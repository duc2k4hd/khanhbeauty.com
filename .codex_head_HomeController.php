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
        // 1. Chỉ gọi Cache tĩnh dạng Mảng thô -> Tuyệt đối không lỗi Serialization (V8 Safe)
        $data = Cache::remember('home_v8_safe', now()->addHours(12), function () {
            return [
                'categories' => ServiceCategory::with(['services' => function($q) {
                    $q->active()->with('featuredImage');
                }])->withCount('services')->active()->get()->toArray(),

                'featuredServices' => Service::with(['category', 'featuredImage'])
                    ->featured()
                    ->active()
                    ->take(6)
                    ->get()->toArray(),

                'latestPortfolios' => Portfolio::with(['beforeImage', 'afterImage'])
                    ->latest()
                    ->take(6)
                    ->get()->toArray(),

                'latestPosts' => Post::with(['category', 'featuredImage'])
                    ->published()
                    ->latest()
                    ->take(3)
                    ->get()->toArray(),

                'brands' => Brand::active()->get()->toArray(),
            ];
        });

        // 2. Tái tạo dữ liệu và ép kiểu Object để View không bị lỗi property access
        $categories = collect($data['categories'])->map(function($c) {
            $cat = (object) $c;
            $cat->services = collect($c['services'] ?? [])->map(function($s) {
                $sv = (object) $s;
                if (isset($sv->featured_image)) $sv->featuredImage = (object) $sv->featured_image;
                return $sv;
            });
            return $cat;
        });

        $featuredServices = collect($data['featuredServices'])->map(function($s) {
            $sv = (object) $s;
            if (isset($sv->featured_image)) $sv->featuredImage = (object) $sv->featured_image;
            if (isset($sv->category)) $sv->category = (object) $sv->category;
            return $sv;
        });

        $latestPortfolios = collect($data['latestPortfolios'])->map(function($p) {
            $port = (object) $p;
            if (isset($port->before_image)) $port->beforeImage = (object) $port->before_image;
            if (isset($port->after_image)) $port->afterImage = (object) $port->after_image;
            return $port;
        });

        $latestPosts = collect($data['latestPosts'])->map(function($p) {
            $post = (object) $p;
            if (isset($post->category)) $post->category = (object) $post->category;
            if (isset($post->featured_image)) $post->featuredImage = (object) $post->featured_image;
            return $post;
        });

        $brands = collect($data['brands'])->map(fn($b) => (object) $b);

        // 3. SEO Configuration
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
