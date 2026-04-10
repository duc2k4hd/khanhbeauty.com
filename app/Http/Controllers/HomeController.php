<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use App\Services\MediaUploadService;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('home_v8_safe', now()->addHours(12), function () {
            return [
                'categories' => ServiceCategory::with(['services' => function ($query) {
                    $query->active()->with('featuredImage');
                }])->withCount('services')->active()->get()->toArray(),

                'featuredServices' => Service::with(['category', 'featuredImage'])
                    ->featured()
                    ->active()
                    ->take(6)
                    ->get()
                    ->toArray(),

                'latestPortfolios' => Portfolio::with(['beforeImage', 'afterImage'])
                    ->latest()
                    ->take(6)
                    ->get()
                    ->toArray(),

                'latestPosts' => Post::with(['category', 'featuredImage'])
                    ->published()
                    ->latest()
                    ->take(3)
                    ->get()
                    ->toArray(),

                'brands' => Brand::active()->get()->toArray(),
            ];
        });

        $categories = collect($data['categories'])->map(function ($category) {
            $item = (object) $category;
            $item->services = collect($category['services'] ?? [])->map(function ($service) {
                $entry = (object) $service;
                $entry->featuredImage = $this->toObject($service['featured_image'] ?? null);

                return $entry;
            });

            return $item;
        });

        $featuredServices = collect($data['featuredServices'])->map(function ($service) {
            $item = (object) $service;
            $item->featuredImage = $this->toObject($service['featured_image'] ?? null);
            $item->category = $this->toObject($service['category'] ?? null);

            return $item;
        });

        $latestPortfolios = collect($data['latestPortfolios'])->map(function ($portfolio) {
            $item = (object) $portfolio;
            $item->beforeImage = $this->toObject($portfolio['before_image'] ?? null);
            $item->afterImage = $this->toObject($portfolio['after_image'] ?? null);

            return $item;
        });

        $latestPosts = collect($data['latestPosts'])->map(function ($post) {
            $item = (object) $post;
            $item->category = $this->toObject($post['category'] ?? null);
            $item->featuredImage = $this->toObject($post['featured_image'] ?? null);

            return $item;
        });

        $brands = collect($data['brands'])->map(fn ($brand) => (object) $brand);

        $hero = [
            'badge' => $this->stringValue(SiteSetting::getValue('hero_badge', '✦ Professional Makeup Artist ✦')),
            'title' => $this->stringValue(SiteSetting::getValue('hero_title', 'Khánh Beauty')),
            'subtitle' => $this->stringValue(SiteSetting::getValue('hero_subtitle', 'Nghệ thuật trang điểm — Tôn vinh vẻ đẹp của bạn')),
            'cta_primary' => $this->stringValue(SiteSetting::getValue('hero_cta_primary', 'Đặt Lịch Makeup')),
            'cta_secondary' => $this->stringValue(SiteSetting::getValue('hero_cta_secondary', 'Xem Portfolio')),
        ];

        $about = [
            'label' => $this->stringValue(SiteSetting::getValue('about_label', 'Về tôi')),
            'title' => $this->stringValue(SiteSetting::getValue('about_title', 'Xin chào, mình là <em>Khánh</em> — người sẽ giúp bạn toả sáng')),
            'desc_1' => $this->stringValue(SiteSetting::getValue('about_desc_1', 'Với niềm đam mê trang điểm từ nhỏ, mình đã dành nhiều năm học hỏi và trau dồi kỹ năng để mang đến cho mỗi khách hàng một diện mạo hoàn hảo nhất. Từ cô dâu trong ngày trọng đại, đến các bạn sinh viên muốn tự tin hơn — mình luôn lắng nghe và thấu hiểu.')),
            'desc_2' => $this->stringValue(SiteSetting::getValue('about_desc_2', 'Không chỉ là makeup, mình muốn mỗi lần ngồi trước gương cùng bạn là một trải nghiệm vui vẻ, thoải mái và đáng nhớ.')),
            'image' => MediaUploadService::url((int) SiteSetting::getValue('about_image_id')) ?? '/images/clients/about.png',
            'stats' => $this->normalizeStatItems(json_decode(SiteSetting::getValue('about_stats', '[]'), true) ?: [
                ['value' => '500', 'label' => 'Khách hàng'],
                ['value' => '200', 'label' => 'Cô dâu'],
                ['value' => '50', 'label' => 'Học viên'],
            ]),
        ];

        $showcase = [
            'label' => $this->stringValue(SiteSetting::getValue('showcase_label', 'Kỹ năng thực chiến')),
            'title' => $this->stringValue(SiteSetting::getValue('showcase_title', 'Từng đường nét, từng chi tiết — đều là nghệ thuật')),
            'items' => $this->normalizeShowcaseItems(json_decode(SiteSetting::getValue('showcase_items', '[]'), true) ?: [
                [
                    'image_url' => '/images/clients/service-bridal.png',
                    'tag' => '✦ Makeup Cô Dâu',
                    'step' => '01',
                    'title' => 'Kẻ Mắt — Đôi mắt kể câu chuyện',
                    'description' => 'Mỗi đôi mắt có một hình dáng riêng. Mình không áp dụng công thức chung cho tất cả — mà sẽ phân tích dáng mắt, hốc mắt để tạo đường kẻ eyeliner phù hợp nhất.',
                    'skills' => ['Cat Eye', 'Puppy Eye', 'Smoky Eye', 'Cut Crease'],
                ],
                [
                    'image_url' => '/images/clients/service-event.png',
                    'tag' => '✦ Makeup Sự Kiện',
                    'step' => '02',
                    'title' => 'Đánh Son — Nụ cười thêm rạng rỡ',
                    'description' => 'Son không chỉ là tô màu lên môi. Mình sẽ chọn tông son phù hợp tông da, bối cảnh, trang phục.',
                    'skills' => ['Ombre Lips', 'Gradient Lips', 'Full Lips', 'Overlining'],
                ],
                [
                    'image_url' => '/images/clients/service-class.png',
                    'tag' => '✦ Đào tạo Makeup',
                    'step' => '03',
                    'title' => 'Contour & Highlight — Gương mặt 3D tự nhiên',
                    'description' => 'Contour đúng cách không phải để "fake" mà để tôn vinh đường nét sẵn có.',
                    'skills' => ['Soft Contour', 'Baking', 'Strobing', 'Glass Skin'],
                ],
            ]),
        ];

        $testimonialsSection = [
            'label' => $this->stringValue(SiteSetting::getValue('testimonials_label', 'Khách hàng nói gì')),
            'title' => $this->stringValue(SiteSetting::getValue('testimonials_title', 'Những lời yêu thương mình nhận được')),
            'items' => $this->normalizeTestimonials(json_decode(SiteSetting::getValue('testimonials', '[]'), true) ?: [
                ['stars' => 5, 'text' => 'Khánh makeup cho mình ngày cưới, ai cũng khen đẹp tự nhiên mà vẫn rạng rỡ.', 'name' => 'Minh Anh', 'role' => 'Cô dâu — Hà Nội', 'avatar' => 'MA'],
                ['stars' => 5, 'text' => 'Mình book Khánh cho buổi chụp kỷ yếu cả lớp. Bạn ấy makeup nhanh, đẹp mà giá cả hợp lý lắm.', 'name' => 'Thuỳ Linh', 'role' => 'Sinh viên — Đà Nẵng', 'avatar' => 'TL'],
                ['stars' => 5, 'text' => 'Lần đầu book makeup online mà gặp được Khánh là may mắn.', 'name' => 'Chị Hương', 'role' => 'Khách book online — Sài Gòn', 'avatar' => 'H'],
                ['stars' => 5, 'text' => 'Mình học khoá makeup cá nhân với Khánh. Giờ mình tự tin trang điểm đi làm mỗi ngày rồi.', 'name' => 'Lan Ngọc', 'role' => 'Học viên — Hà Nội', 'avatar' => 'LN'],
            ]),
        ];

        $numbersStats = $this->normalizeStatItems(json_decode(SiteSetting::getValue('numbers_stats', '[]'), true) ?: [
            ['value' => '500', 'label' => 'Khách hàng hài lòng'],
            ['value' => '200', 'label' => 'Cô dâu xinh đẹp'],
            ['value' => '5', 'label' => 'Năm kinh nghiệm'],
            ['value' => '50', 'label' => 'Học viên đào tạo'],
        ]);

        $metaTitle = $this->stringValue(SiteSetting::getValue('meta_title', 'Khánh Beauty — Dịch Vụ Trang Điểm Chuyên Nghiệp Tại Nhà'));
        $metaDesc = $this->stringValue(SiteSetting::getValue('meta_description', 'Khám phá dịch vụ makeup chuyên nghiệp tại Khánh Beauty. Trang điểm dự tiệc, cô dâu, sự kiện với phong cách tự nhiên, sang trọng ngay tại không gian của bạn.'));
        $metaKeywords = $this->stringValue(SiteSetting::getValue('meta_keywords'));
        $ogImageUrl = $this->stringValue(SiteSetting::getValue('og_image_url'));
        $canonicalUrl = $this->stringValue(SiteSetting::getValue('canonical_url'));

        SEOTools::setTitle($metaTitle);
        SEOTools::setDescription($metaDesc);
        if ($metaKeywords !== '') {
            SEOTools::metatags()->setKeywords(array_map('trim', explode(',', $metaKeywords)));
        }
        if ($canonicalUrl !== '') {
            SEOTools::setCanonical($canonicalUrl);
        }
        SEOTools::opengraph()->addProperty('type', 'website');
        if ($ogImageUrl !== '') {
            SEOTools::opengraph()->addImage(url($ogImageUrl));
        }
        SEOTools::twitter()->setSite('@khanhbeauty');

        $galleryData = Cache::remember('home_gallery_v1', now()->addDays(7), function () {
            $basePath = public_path('images/portfolios');
            $tabs = [];
            $images = [];

            if (is_dir($basePath)) {
                // Quét thư mục con (lấy danh mục)
                $iterator = new \DirectoryIterator($basePath);
                foreach ($iterator as $dirInfo) {
                    if ($dirInfo->isDot()) continue;
                    
                    if ($dirInfo->isDir()) {
                        $folderName = $dirInfo->getFilename();
                        $tabName = mb_convert_case(str_replace('-', ' ', $folderName), MB_CASE_TITLE, 'UTF-8');
                        $tabs[$folderName] = $tabName;
                        
                        $folderPath = $dirInfo->getPathname();
                        $files = glob($folderPath . '/*.{jpg,jpeg,png,webp,avif,JPG,JPEG,PNG,WEBP,AVIF}', GLOB_BRACE);
                        
                        if ($files !== false) {
                            foreach ($files as $file) {
                                $images[] = [
                                    'url' => asset('images/portfolios/' . $folderName . '/' . basename($file)),
                                    'category' => $folderName,
                                    'title' => pathinfo($file, PATHINFO_FILENAME),
                                ];
                            }
                        }
                    } elseif ($dirInfo->isFile()) {
                        // Nếu user vứt ảnh thẳng vào mục gốc (không qua thư mục danh mục)
                        $ext = strtolower($dirInfo->getExtension());
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'avif'])) {
                            $images[] = [
                                'url' => asset('images/portfolios/' . $dirInfo->getFilename()),
                                'category' => 'all', // Không thuộc tab nào cụ thể ngoài "Tất cả"
                                'title' => pathinfo($dirInfo->getFilename(), PATHINFO_FILENAME),
                            ];
                        }
                    }
                }
            }

            if (count($images) > 100) {
                $randomKeys = array_rand($images, 100);
                $selectedImages = [];
                foreach ((array) $randomKeys as $key) {
                    $selectedImages[] = $images[$key];
                }
                $images = $selectedImages;
            } else {
                shuffle($images);
            }

            return [
                'tabs' => empty($tabs) ? ['all' => 'Tất cả'] : ['all' => 'Tất cả'] + $tabs,
                'images' => $images
            ];
        });

        $galleryTabs = $galleryData['tabs'];
        $galleryImages = collect($galleryData['images'])->map(fn ($item) => (object) $item);

        return view('clients.pages.home.index', compact(
            'categories',
            'featuredServices',
            'galleryTabs',
            'galleryImages',
            'latestPortfolios',
            'latestPosts',
            'brands',
            'hero',
            'about',
            'showcase',
            'testimonialsSection',
            'numbersStats'
        ));
    }

    private function stringValue(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            return collect($value)
                ->flatten()
                ->filter(fn ($item) => is_scalar($item) && trim((string) $item) !== '')
                ->map(fn ($item) => (string) $item)
                ->implode(' ');
        }

        return $default;
    }

    private function toObject(mixed $value): ?object
    {
        if (is_array($value)) {
            return (object) $value;
        }

        return is_object($value) ? $value : null;
    }

    private function normalizeStatItems(array $items): array
    {
        return collect($items)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) {
                return [
                    'value' => $this->stringValue($item['value'] ?? ''),
                    'label' => $this->stringValue($item['label'] ?? ''),
                ];
            })
            ->filter(fn (array $item) => $item['value'] !== '' && $item['label'] !== '')
            ->values()
            ->all();
    }

    private function normalizeShowcaseItems(array $items): array
    {
        return collect($items)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item, int $index) {
                $skills = $item['skills'] ?? [];

                if (is_string($skills)) {
                    $skills = explode(',', $skills);
                }

                return [
                    'image_url' => $this->stringValue($item['image_url'] ?? '/images/no-image.webp', '/images/no-image.webp'),
                    'tag' => $this->stringValue($item['tag'] ?? ''),
                    'step' => $this->stringValue($item['step'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)),
                    'title' => $this->stringValue($item['title'] ?? ''),
                    'description' => $this->stringValue($item['description'] ?? ''),
                    'skills' => collect(is_array($skills) ? $skills : [])
                        ->filter(fn ($skill) => is_scalar($skill) && trim((string) $skill) !== '')
                        ->map(fn ($skill) => (string) $skill)
                        ->values()
                        ->all(),
                ];
            })
            ->filter(fn (array $item) => $item['title'] !== '')
            ->values()
            ->all();
    }

    private function normalizeTestimonials(array $items): array
    {
        return collect($items)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) {
                return [
                    'stars' => max(1, min(5, (int) ($item['stars'] ?? 5))),
                    'text' => $this->stringValue($item['text'] ?? ''),
                    'name' => $this->stringValue($item['name'] ?? ''),
                    'role' => $this->stringValue($item['role'] ?? ''),
                    'avatar' => $this->stringValue($item['avatar'] ?? ''),
                ];
            })
            ->filter(fn (array $item) => $item['text'] !== '' && $item['name'] !== '')
            ->values()
            ->all();
    }
}
