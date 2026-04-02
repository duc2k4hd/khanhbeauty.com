<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\User;

class KhanhBeautySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* 1. Tạo Tài Khoản User, Admin & Chuyên gia Makeup (Staff) */
        $staffs = [
            ['full_name' => 'Khánh Administrator', 'email' => 'admin@khanhbeauty.com', 'phone' => '0999999999', 'role' => 'admin'],
            ['full_name' => 'Khánh Makeup', 'email' => 'khanh@beauty.com', 'phone' => '0901234567', 'role' => 'staff'],
            ['full_name' => 'Lan Phạm', 'email' => 'lan@beauty.com', 'phone' => '0912345678', 'role' => 'staff'],
            ['full_name' => 'Hương Trà', 'email' => 'huong@beauty.com', 'phone' => '0923456789', 'role' => 'staff'],
        ];

        foreach ($staffs as $staff) {
            User::firstOrCreate(
                ['email' => $staff['email']],
                [
                    'full_name' => $staff['full_name'],
                    'phone' => $staff['phone'],
                    'password' => Hash::make('12345678'),
                    'role' => $staff['role'],
                    'is_active' => true,
                ]
            );
        }

        /* 2. Tạo Danh mục Dịch vụ (Service Category) */
        $categories = [
            'Makeup Cô Dâu', 'Makeup Sự Kiện', 'Khoá Học Makeup', 'Chăm Sóc Đi Tiệc'
        ];
        $catIds = [];

        foreach ($categories as $index => $catName) {
            $cat = ServiceCategory::firstOrCreate(
                ['slug' => Str::slug($catName)],
                [
                    'name' => $catName,
                    'description' => 'Chuyên trang ' . $catName . ' chuyên nghiệp bởi Khánh Beauty.',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
            $catIds[$catName] = $cat->id;
        }

        /* 3. Tạo Dịch vụ Chi Tiết (Service) dựa trên mẫu trang chủ */
        $services = [
            [
                'category_id' => $catIds['Makeup Cô Dâu'],
                'name' => 'Makeup Cô Dâu',
                'slug' => Str::slug('Makeup Cô Dâu'),
                'short_description' => 'Trang điểm chuyên nghiệp cho ngày cưới. Bền đẹp suốt 12 giờ, phù hợp concept & áo dài / váy cưới.',
                'description' => 'Dành riêng cho ngày trọng đại, mình sẽ tư vấn kỹ càng để thiết kế layout làm nổi bật vẻ đẹp tự nhiên của bạn. Sử dụng mỹ phẩm cao cấp đảm bảo lớp nền bền đẹp.',
                'price' => 2500000,
                'price_unit' => 'buổi',
                'duration_minutes' => 120,
                'is_featured' => true,
                'is_active' => true,
                'featured_image_url' => '/images/clients/service-bridal.png',
                'variants' => [
                    ['name' => 'Làm tại Studio', 'price' => 2300000, 'duration_minutes' => 90, 'sku' => 'BRIDE-STUDIO'],
                    ['name' => 'Làm tại nhà', 'price' => 2500000, 'duration_minutes' => 120, 'sku' => 'BRIDE-HOME'],
                ]
            ],
            [
                'category_id' => $catIds['Makeup Sự Kiện'],
                'name' => 'Makeup Sự Kiện',
                'slug' => Str::slug('Makeup Sự Kiện'),
                'short_description' => 'Trang điểm cho tiệc, lễ tốt nghiệp, chụp ảnh profile. Tone makeup nhẹ nhàng, tự nhiên hoặc glamour tùy ý.',
                'description' => 'Giúp bạn tỏa sáng trong mọi buổi tiệc. Bạn có thể chọn phong cách Hàn Quốc, Thái Lan hoặc Tây Âu.',
                'price' => 800000,
                'price_unit' => 'buổi',
                'duration_minutes' => 60,
                'is_featured' => true,
                'is_active' => true,
                'featured_image_url' => '/images/clients/service-event.png',
                'variants' => [
                    ['name' => 'Tiệc tối / Sự kiện', 'price' => 800000, 'duration_minutes' => 60, 'sku' => 'EVENT-NORMAL'],
                ]
            ],
            [
                'category_id' => $catIds['Makeup Sự Kiện'],
                'name' => 'Makeup Chụp Ảnh',
                'slug' => Str::slug('Makeup Chụp Ảnh'),
                'short_description' => 'Phối hợp cùng photographer để tạo look hoàn hảo cho shoot ảnh concept, lookbook, bộ ảnh kỷ yếu.',
                'description' => 'Đảm bảo lớp trang điểm lên hình sắc nét và đúng ý đồ nghệ thuật của bộ ảnh.',
                'price' => 1000000,
                'price_unit' => 'buổi',
                'duration_minutes' => 90,
                'is_featured' => true,
                'is_active' => true,
                'featured_image_url' => '/images/clients/service-event.png',
                'variants' => [
                    ['name' => 'Concept / Lookbook', 'price' => 1000000, 'duration_minutes' => 90, 'sku' => 'PHOTO-CONCEPT'],
                ]
            ],
            [
                'category_id' => $catIds['Chăm Sóc Đi Tiệc'],
                'name' => 'Makeup Nhẹ Nhàng',
                'slug' => Str::slug('Makeup Nhẹ Nhàng'),
                'short_description' => 'Dành cho các bạn sinh viên, dự tiệc nhẹ, đi chơi cuối tuần. Makeup tự nhiên, trong trẻo, đúng tuổi.',
                'description' => 'Lớp makeup mỏng nhẹ như không, giúp bạn tự tin xuống phố cùng bạn bè.',
                'price' => 400000,
                'price_unit' => 'lượt',
                'duration_minutes' => 45,
                'is_featured' => true,
                'is_active' => true,
                'featured_image_url' => '/images/clients/service-event.png',
                'variants' => [
                    ['name' => 'Daily / Dating', 'price' => 400000, 'duration_minutes' => 45, 'sku' => 'DAILY-LOOK'],
                ]
            ],
            [
                'category_id' => $catIds['Khoá Học Makeup'],
                'name' => 'Đào Tạo Makeup',
                'slug' => Str::slug('Đào Tạo Makeup'),
                'short_description' => 'Khoá học 1-1 hoặc nhóm nhỏ. Từ cơ bản đến nâng cao, mình sẽ dạy bạn cách tự trang điểm đẹp mỗi ngày.',
                'description' => 'Hướng dẫn chi tiết từ cách chăm sóc da, chọn mỹ phẩm đến các kỹ thuật trang điểm chuyên nghiệp.',
                'price' => 3000000,
                'price_unit' => 'khóa',
                'duration_minutes' => 180,
                'is_featured' => true,
                'is_active' => true,
                'featured_image_url' => '/images/clients/service-class.png',
                'variants' => [
                    ['name' => 'Khoá cá nhân 5 buổi', 'price' => 3000000, 'duration_minutes' => 450, 'sku' => 'COURSE-PERSONAL'],
                ]
            ],
            [
                'category_id' => $catIds['Chăm Sóc Đi Tiệc'],
                'name' => 'Tư Vấn Skincare',
                'slug' => Str::slug('Tư Vấn Skincare'),
                'short_description' => 'Tư vấn chăm sóc da phù hợp với từng loại da, giúp bạn có làn da khoẻ đẹp — nền tảng của mọi lớp makeup.',
                'description' => 'Phân tích loại da và đề xuất quy trình dưỡng da tối ưu cho riêng bạn.',
                'price' => 0,
                'price_unit' => 'người',
                'duration_minutes' => 30,
                'is_featured' => true,
                'is_active' => true,
                'featured_image_url' => '/images/clients/about.png',
                'variants' => [
                    ['name' => 'Tư vấn miễn phí', 'price' => 0, 'duration_minutes' => 30, 'sku' => 'SKINCARE-FREE'],
                ]
            ]
        ];

        /* Nạp dữ liệu Service vào Bảng */
        foreach ($services as $srvData) {
            $variantsData = $srvData['variants'];
            unset($srvData['variants']); 

            // Đăng ký vào bảng Media để hiển thị trong Media Library
            if (!empty($srvData['featured_image_url'])) {
                $media = $this->registerMedia($srvData['featured_image_url'], 'services', $srvData['name']);
                $srvData['featured_image_id'] = $media->id;
            }
            unset($srvData['featured_image_url']);

            $service = Service::firstOrCreate(
                ['slug' => $srvData['slug']],
                $srvData
            );

            // Nạp Variants vào bảng
            foreach ($variantsData as $index => $var) {
                \App\Models\ServiceVariant::firstOrCreate(
                    ['service_id' => $service->id, 'variant_name' => $var['name']],
                    [
                        'price' => $var['price'],
                        'duration_minutes' => $var['duration_minutes'],
                        'sku' => $var['sku'],
                        'is_active' => true,
                    ]
                );
            }
        }

        /* 4. Tạo Dữ liệu Portfolio (Trước/Sau & Gallery) */
        $portfolios = [
            [
                'title' => 'Make-over Cô Dâu Thùy Dương',
                'slug' => 'make-over-co-dau-thuy-duong',
                'category' => 'bride',
                'before_image' => 'https://images.unsplash.com/photo-1596704017254-9b121068fb29?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
                'after_image' => '/images/clients/portfolio-1.png',
                'is_featured' => true,
            ],
            [
                'title' => 'Layout Sự Kiện High Fashion',
                'slug' => 'layout-su-kien-high-fashion',
                'category' => 'event',
                'before_image' => 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
                'after_image' => '/images/clients/portfolio-2.png',
                'is_featured' => true,
            ]
        ];
        foreach ($portfolios as $p) {
            // Đăng ký vào bảng Media
            if (!empty($p['before_image'])) {
                $mediaBefore = $this->registerMedia($p['before_image'], 'portfolios', $p['title'] . ' Before');
                $p['before_image_id'] = $mediaBefore->id;
            }
            if (!empty($p['after_image'])) {
                $mediaAfter = $this->registerMedia($p['after_image'], 'portfolios', $p['title'] . ' After');
                $p['after_image_id'] = $mediaAfter->id;
            }
            unset($p['before_image'], $p['after_image']);

            \App\Models\Portfolio::firstOrCreate(['slug' => $p['slug']], $p);
        }

        /* 5. Cấu hình cài đặt trang web (Site Settings) */
        $settings = [
            ['setting_key' => 'site_name', 'setting_value' => 'Khánh Beauty - MakeUp Academy', 'setting_group' => 'general'],
            ['setting_key' => 'phone', 'setting_value' => '0987.654.321', 'setting_group' => 'contact'],
            ['setting_key' => 'email', 'setting_value' => 'booking@khanhbeauty.com', 'setting_group' => 'contact'],
            ['setting_key' => 'address', 'setting_value' => '123 Đường Sắc Đẹp, Quận 1, TP Hồ Chí Minh', 'setting_group' => 'contact'],
            ['setting_key' => 'working_hours', 'setting_value' => '08:00 - 20:00 (Tất cả các ngày trong tuần)', 'setting_group' => 'contact'],
            ['setting_key' => 'facebook_url', 'setting_value' => 'https://facebook.com/khanhbeauty', 'setting_group' => 'social'],
            ['setting_key' => 'tiktok_url', 'setting_value' => 'https://tiktok.com/@khanhbeauty', 'setting_group' => 'social'],
            ['setting_key' => 'instagram_url', 'setting_value' => 'https://instagram.com/khanhbeauty__', 'setting_group' => 'social'],
            ['setting_key' => 'google_map', 'setting_value' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4602324211153!2d106.6972322147183!3d10.776019492321852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3fcccbc67f%3A0xebea81123a07a61d!2sBen%20Thanh%20Market!5e0!3m2!1sen!2svn!4v1655180451556!5m2!1sen!2svn" width="100%" height="250" style="border:0; border-radius: 16px;" allowfullscreen="" loading="lazy"></iframe>', 'setting_group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            \App\Models\SiteSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                ['setting_value' => $setting['setting_value'], 'setting_group' => $setting['setting_group']]
            );
        }

    }

    /**
     * Helper: Đăng ký tệp vào bảng Media
     */
    private function registerMedia($url, $folder, $title)
    {
        return \App\Models\Media::firstOrCreate(
            ['file_url' => $url],
            [
                'file_name' => basename($url),
                'file_path' => str_replace('/storage/', '', $url), // Giả định path nếu ở storage
                'disk' => 'public',
                'mime_type' => 'image/' . pathinfo($url, PATHINFO_EXTENSION),
                'file_size_bytes' => 0,
                'folder' => $folder,
                'title' => $title,
                'image_type' => 'image',
                'is_optimized' => true,
            ]
        );
    }
}
