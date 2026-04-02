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
        $data = Cache::remember('services_index', now()->addHours(12), function () {
            return [
                'services' => Service::active()->with(['category', 'featuredImage'])->get(),
                'categories' => ServiceCategory::active()->get(),
            ];
        });

        $services = $data['services'];
        $categories = $data['categories'];

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
        // 1. Tối ưu Truy vấn & Cache (6 tiếng)
        $cacheKey = 'service_page_' . $slug;
        $service = Cache::remember($cacheKey, now()->addHours(6), function () use ($slug) {
            return Service::where('slug', $slug)
                ->with([
                    'category', 
                    'featuredImage',
                    'variants' => fn($q) => $q->active(),
                    'faqs' => fn($q) => $q->active()->orderBy('sort_order', 'asc'), 
                    'reviews' => fn($q) => $q->approved()->latest()
                ])
                ->where('is_active', true)
                ->firstOrFail();
        });

        // 2. Chạy ngầm Tăng Lượt Xem & Debounce theo Session
        $sessionKey = 'viewed_service_' . $service->id;
        if (!session()->has($sessionKey)) {
            // Sử dụng defer/dispatch để tác vụ SQL chạy sau khi HTML đã trả về cho người dùng
            if (function_exists('defer')) {
                defer(fn() => Service::where('id', $service->id)->increment('view_count'));
            } else {
                dispatch(fn() => Service::where('id', $service->id)->increment('view_count'))->afterResponse();
            }
            session()->put($sessionKey, true);
        }

        // 3. Dịch vụ liên quan (Cache đệm)
        $relatedCacheKey = 'related_services_' . $service->category_id;
        $relatedServices = Cache::remember($relatedCacheKey, now()->addHours(12), function () use ($service) {
            return Service::where('category_id', $service->category_id)
                ->where('id', '!=', $service->id)
                ->with(['category', 'featuredImage'])
                ->active()
                ->take(3)
                ->get();
        });

        // 4. Cấu hình SEO Meta nâng cao
        SEOTools::setTitle(($service->meta_title ?: $service->name) . ' - Khánh Beauty');
        SEOTools::setDescription($service->meta_description ?: $service->short_description);
        SEOTools::metatags()->addKeyword($service->meta_keywords ?: $service->name);
        SEOTools::opengraph()->addProperty('type', 'article'); // Article/Product thay vì website
        
        if ($service->featuredImage) {
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
                'price' => $service->sale_price ?: $service->price,
                'availability' => 'https://schema.org/InStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ]
        ];

        if ($service->avg_rating > 0 && $service->reviews->count() > 0) {
            $productSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $service->avg_rating,
                'reviewCount' => $service->reviews->count(),
            ];
        }
        
        SEOTools::jsonLdMulti()->addValue('Product', $productSchema);

        // 5.2 FAQPage Schema (Hỏi đáp nhanh trên Google)
        if ($service->faqs && $service->faqs->count() > 0) {
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
