<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    /**
     * Hiển thị danh sách toàn bộ dịch vụ công khai
     */
    public function index()
    {
        $data = Cache::remember('services_index_safe', now()->addHours(12), function () {
            return [
                'services' => Service::active()->with(['category', 'featuredImage'])->get()->toArray(),
                'categories' => ServiceCategory::active()->get()->toArray(),
            ];
        });

        $services = collect($data['services'])->map(function($item) {
            $obj = (object) $item;
            if (isset($obj->category)) $obj->category = (object) $obj->category;
            if (isset($obj->featured_image)) $obj->featuredImage = (object) $obj->featured_image;
            return $obj;
        });

        $categories = collect($data['categories'])->map(function($item) {
            return (object) $item;
        });

        SEOTools::setTitle('Dịch vụ Trang điểm Chuyên nghiệp - Khánh Beauty');
        SEOTools::setDescription('Khám phá trọn bộ dịch vụ làm đẹp tại nhà của Khánh Beauty: Trang điểm cô dâu, dự tiệc, sự kiện, khóa học makeup cá nhân với chất lượng hàng đầu.');
        SEOTools::opengraph()->addProperty('type', 'website');
        
        return view('clients.pages.service.index', compact('services', 'categories'));
    }

    /**
     * Hiển thị trang Landing Page chi tiết của dịch vụ
     */
    public function show($slug)
    {
        // 1. Tối ưu Truy vấn & Cache (Lưu dạng Mảng thô để triệt tiêu lỗi Incomplete Object 500)
        $v7Key = 'service_v7_safe_' . $slug; 
        $serviceData = Cache::remember($v7Key, now()->addHours(6), function () use ($slug) {
            return Service::where('slug', $slug)
                ->with([
                    'category', 
                    'featuredImage',
                    'variants' => fn($q) => $q->active(),
                    'faqs' => fn($q) => $q->active()->orderBy('sort_order', 'asc'), 
                    'reviews' => fn($q) => $q->approved()->latest()
                ])
                ->where('is_active', true)
                ->firstOrFail()
                ->toArray(); 
        });

        // Tái tạo đối tượng lai: Cấp 1 là Object, các trường JSON giữ nguyên Array cho View
        $service = (object) $serviceData;
        if (isset($service->category)) $service->category = (object) $service->category;
        if (isset($service->featured_image)) $service->featuredImage = (object) $service->featured_image;
        
        // Nạp Accessor gallery_media thủ công vì stdClass không tự hiểu hàm trong Model
        $galleryIds = $serviceData['gallery_ids'] ?? [];
        if (!empty($galleryIds)) {
            $service->gallery_media = \App\Models\Media::whereIn('id', $galleryIds)
                ->get()
                ->sortBy(fn($m) => array_search($m->id, $galleryIds))
                ->values();
        } else {
            $service->gallery_media = collect();
        }

        // Đảm bảo các quan hệ danh sách là Collection để dùng được ->count()
        $service->variants = collect($serviceData['variants'] ?? [])->map(fn($v) => (object) $v);
        $service->faqs = collect($serviceData['faqs'] ?? [])->map(fn($f) => (object) $f);
        $service->reviews = collect($serviceData['reviews'] ?? [])->map(fn($r) => (object) $r);
        
        // 2. Tăng Lượt Xem - Debounce bằng Cache (An toàn hơn Session)
        $viewKey = "view_safe_{$service->id}_" . request()->ip();
        if (Cache::add($viewKey, true, now()->addHour())) {
            \DB::table('services')->where('id', $service->id)->increment('view_count');
        }

        // 3. Dịch vụ liên quan (Cache mảng)
        $relatedKey = 'related_v7_' . $service->category_id;
        $relatedData = Cache::remember($relatedKey, now()->addHours(12), function () use ($service) {
            return Service::where('category_id', $service->category_id)
                ->where('id', '!=', $service->id)
                ->with(['category', 'featuredImage'])
                ->active()
                ->take(3)
                ->get()
                ->toArray();
        });

        // Chuẩn hóa danh sách liên quan cho View
        $relatedServices = collect($relatedData)->map(function($item) {
            $obj = (object) $item;
            if (isset($obj->category)) $obj->category = (object) $obj->category;
            if (isset($obj->featured_image)) $obj->featuredImage = (object) $obj->featured_image;
            return $obj;
        });

        // 4. Cấu hình SEO Meta
        SEOTools::setTitle(($service->meta_title ?: $service->name) . ' - Khánh Beauty');
        SEOTools::setDescription($service->meta_description ?: $service->short_description);
        SEOTools::metatags()->addKeyword($service->meta_keywords ?: $service->name);
        SEOTools::opengraph()->addProperty('type', 'article'); 
        
        if (isset($service->featuredImage->file_url)) {
            SEOTools::opengraph()->addImage(asset($service->featuredImage->file_url));
            SEOTools::twitter()->setImage(asset($service->featuredImage->file_url));
        }

        // ==========================================
        // 5. CHUẨN HÓA CẤU TRÚC JSON-LD (RICH SNIPPETS)
        // ==========================================
        
        // 5.1 Product Schema (Sao đánh giá và Giá bán trên Google)
        $productSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $service->name,
            'image' => $service->featuredImage ? asset($service->featuredImage->file_url) : asset('images/logo.png'),
            'description' => $service->short_description,
            'sku' => 'SRV-' . $service->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Khánh Beauty'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('services.show', $service->slug),
                'priceCurrency' => 'VND',
                'price' => ($service->sale_price ?? null) ?: $service->price,
                'availability' => 'https://schema.org/InStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ]
        ];

        if (($service->avg_rating ?? 0) > 0 && $service->reviews->count() > 0) {
            $productSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $service->avg_rating,
                'reviewCount' => $service->reviews->count(),
            ];
        }
        
        SEOTools::jsonLdMulti()->addValue('Product', $productSchema);

        // 5.2 FAQPage Schema (Hỏi đáp nhanh trên Google)
        if ($service->faqs->count() > 0) {
            $faqSchema = [
                '@context' => 'https://schema.org/',
                '@type' => 'FAQPage',
                'mainEntity' => []
            ];
            foreach ($service->faqs as $faq) {
                $faqSchema['mainEntity'][] = [
                    '@type' => 'Question',
                    'name' => $faq->question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq->answer),
                    ],
                ];
            }
            SEOTools::jsonLdMulti()->addValue('FAQPage', $faqSchema);
        }

        return view('clients.pages.service.show', compact('service', 'relatedServices'));
    }
}
