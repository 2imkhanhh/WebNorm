<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Service;
use App\Models\Blog;
use App\Models\Review;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@norm.vn'],
            [
                'name' => 'Admin Norm',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Settings (Thương hiệu, Liên hệ, 3D model, Mạng xã hội)
        $settings = [
            ['key' => 'site_name', 'value' => ['vi' => 'Brand Design Agency • Norm', 'en' => 'Brand Design Agency • Norm'], 'group' => 'general', 'description' => 'Tên thương hiệu website'],
            ['key' => 'logo', 'value' => '/assets/images/logo.png', 'group' => 'general', 'description' => 'Ảnh logo chính'],
            ['key' => 'model_3d', 'value' => '/storage/uploads/norm.glb', 'group' => 'general', 'description' => 'Đường dẫn mô hình 3D hero'],
            ['key' => 'hotline', 'value' => '(+84)816 656718', 'group' => 'contact', 'description' => 'Số điện thoại hotline'],
            ['key' => 'email', 'value' => 'info@norm.vn', 'group' => 'contact', 'description' => 'Địa chỉ email'],
            ['key' => 'address', 'value' => ['vi' => "Số 110 Nguyễn Ngọc Nại, P. Phương Liệt, TP. Hà Nội", 'en' => "No. 110 Nguyen Ngoc Nai street,\nPhuong Liet ward, Ha Noi city, Vietnam"], 'group' => 'contact', 'description' => 'Địa chỉ văn phòng'],
            ['key' => 'copyright', 'value' => '©2026 All Rights Reserved', 'group' => 'general', 'description' => 'Dòng bản quyền footer'],
            ['key' => 'social_behance', 'value' => 'https://behance.net', 'group' => 'social', 'description' => 'Link Behance'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com', 'group' => 'social', 'description' => 'Link LinkedIn'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com', 'group' => 'social', 'description' => 'Link Instagram'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com', 'group' => 'social', 'description' => 'Link Facebook'],
            ['key' => 'social_dribbble', 'value' => 'https://dribbble.com', 'group' => 'social', 'description' => 'Link Dribbble'],
        ];
        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 3. Page Contents (Các đoạn văn bản, tiêu đề tĩnh trên từng trang)
        $contents = [
            // TRANG CHỦ (/)
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'section_title' => 'Hero & Tiêu đề chính',
                'field_key' => 'hero_tagline_1',
                'field_label' => 'Banner Tagline 1',
                'field_type' => 'text',
                'value' => ['vi' => 'Brand Design Agency', 'en' => 'Brand Design Agency'],
                'sort_order' => 1,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'section_title' => 'Hero & Tiêu đề chính',
                'field_key' => 'hero_tagline_2',
                'field_label' => 'Banner Tagline 2',
                'field_type' => 'text',
                'value' => ['vi' => 'Based In Vietnam', 'en' => 'Based In Vietnam'],
                'sort_order' => 2,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'section_title' => 'Hero & Tiêu đề chính',
                'field_key' => 'hero_tagline_3',
                'field_label' => 'Banner Tagline 3',
                'field_type' => 'text',
                'value' => ['vi' => 'Culture Your Brand', 'en' => 'Culture Your Brand'],
                'sort_order' => 3,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'section_title' => 'Hero & Tiêu đề chính',
                'field_key' => 'hero_heading',
                'field_label' => 'Tiêu đề lớn Hero (3 dòng)',
                'field_type' => 'textarea',
                'value' => ['vi' => "NHẬN DIỆN\nCHIẾN LƯỢC\nVĂN HOÁ", 'en' => "IDENTITY\nSTRATEGY\nCULTURE"],
                'sort_order' => 4,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'section_title' => 'Hero & Tiêu đề chính',
                'field_key' => 'hero_desc',
                'field_label' => 'Mô tả ngắn trang chủ',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chúng tôi giúp doanh nghiệp xây dựng bản sắc thương hiệu rõ ràng, nhất quán và phù hợp với định hướng kinh doanh.',
                    'en' => 'We help businesses build clear, consistent brand identities aligned with their business direction.'
                ],
                'sort_order' => 5,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'section_title' => 'Hero & Tiêu đề chính',
                'field_key' => 'hero_video',
                'field_label' => 'Tệp giới thiệu',
                'field_type' => 'video',
                'value' => [
                    'vi' => 'storage/uploads/main-feature.mp4',
                    'en' => 'storage/uploads/main-feature.mp4'
                ],
                'sort_order' => 6,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_about',
                'section_title' => 'Khối Giới thiệu (Về chúng tôi)',
                'field_key' => 'about_label',
                'field_label' => 'Nhãn tiêu đề',
                'field_type' => 'text',
                'value' => ['vi' => 'Về chúng tôi', 'en' => 'About us'],
                'sort_order' => 7,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_about',
                'section_title' => 'Khối Giới thiệu (Về chúng tôi)',
                'field_key' => 'about_desc',
                'field_label' => 'Nội dung mô tả',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Có rất nhiều lý do tại sao xây dựng thương hiệu và tiếp thị lại quan trọng. Vô số sách viết về chủ đề này, nhưng tất cả đều quy về một điều đơn giản: tại sao mọi người nên quan tâm đến sự tồn tại của bạn? Chúng tôi tạo ra những trải nghiệm khiến mọi người chú ý và quan tâm.',
                    'en' => 'There are countless reasons why branding and marketing matter. Endless books have been written on this topic, but it all comes down to one simple question: why should anyone care about your existence? We create experiences that make people notice and care.',
                ],
                'sort_order' => 8,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_about',
                'section_title' => 'Khối Giới thiệu (Về chúng tôi)',
                'field_key' => 'about_btn_text',
                'field_label' => 'Chữ trên nút bấm',
                'field_type' => 'text',
                'value' => ['vi' => 'ĐỌC THÊM', 'en' => 'READ MORE'],
                'sort_order' => 9,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_about',
                'section_title' => 'Khối Giới thiệu (Về chúng tôi)',
                'field_key' => 'about_btn_url',
                'field_label' => 'Đường dẫn liên kết nút',
                'field_type' => 'link',
                'value' => ['vi' => '/about-us', 'en' => '/about-us'],
                'sort_order' => 10,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_services',
                'section_title' => 'Khối Giới thiệu Dịch vụ',
                'field_key' => 'services_label',
                'field_label' => 'Nhãn tiêu đề',
                'field_type' => 'text',
                'value' => ['vi' => '• Dịch vụ', 'en' => '• Services'],
                'sort_order' => 12,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_services',
                'section_title' => 'Khối Giới thiệu Dịch vụ',
                'field_key' => 'services_statement',
                'field_label' => 'Tuyên ngôn dịch vụ (Statement)',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chúng tôi giúp doanh nghiệp xây dựng thương hiệu rõ ràng, nhất quán để truyền tải giá trị khác biệt và tăng trưởng.',
                    'en' => 'We help businesses build clear, consistent brands to convey distinct value and drive growth.',
                ],
                'sort_order' => 13,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_services',
                'section_title' => 'Khối Giới thiệu Dịch vụ',
                'field_key' => 'services_desc',
                'field_label' => 'Mô tả ngắn bên phải',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Từ chiến lược đến trải nghiệm, chúng tôi định hình thương hiệu rõ nét.',
                    'en' => 'From strategy to experience, we shape distinct brands.',
                ],
                'sort_order' => 14,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_testimonials',
                'section_title' => 'Khối Trải nghiệm khách hàng',
                'field_key' => 'testimonial_title_1',
                'field_label' => 'Dòng tiêu đề 1 (bên trái)',
                'field_type' => 'text',
                'value' => ['vi' => 'TRẢI NGHIỆM', 'en' => 'CLIENT'],
                'sort_order' => 15,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_testimonials',
                'section_title' => 'Khối Trải nghiệm khách hàng',
                'field_key' => 'testimonial_title_2',
                'field_label' => 'Dòng tiêu đề 2 (bên phải)',
                'field_type' => 'text',
                'value' => ['vi' => 'KHÁCH HÀNG', 'en' => 'EXPERIENCES'],
                'sort_order' => 16,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_blog',
                'section_title' => 'Khối Giới thiệu Blog',
                'field_key' => 'blog_label',
                'field_label' => 'Nhãn tiêu đề',
                'field_type' => 'text',
                'value' => ['vi' => 'Blog', 'en' => 'Blog'],
                'sort_order' => 17,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_blog',
                'section_title' => 'Khối Giới thiệu Blog',
                'field_key' => 'blog_statement',
                'field_label' => 'Tuyên ngôn bài viết (Statement)',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chia sẻ góc nhìn về thương hiệu, thiết kế và chiến lược giúp doanh nghiệp phát triển bền vững.',
                    'en' => 'Sharing perspectives on branding, design, and strategy to help businesses grow sustainably.',
                ],
                'sort_order' => 18,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_blog',
                'section_title' => 'Khối Giới thiệu Blog',
                'field_key' => 'blog_btn_text',
                'field_label' => 'Chữ trên nút xem thêm',
                'field_type' => 'text',
                'value' => ['vi' => 'XEM THÊM BÀI VIẾT', 'en' => 'VIEW ALL ARTICLES'],
                'sort_order' => 19,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'intro_blog',
                'section_title' => 'Khối Giới thiệu Blog',
                'field_key' => 'blog_btn_url',
                'field_label' => 'Đường dẫn liên kết nút',
                'field_type' => 'link',
                'value' => ['vi' => '/news', 'en' => '/news'],
                'sort_order' => 20,
            ],

            // VỀ CHÚNG TÔI (/about-us)
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_intro',
                'section_title' => 'Giới thiệu & Giải pháp',
                'field_key' => 'about_heading_1',
                'field_label' => 'Tiêu đề dòng 1 (trái)',
                'field_type' => 'text',
                'value' => ['vi' => 'GIẢI PHÁP', 'en' => 'DESIGN SOLUTIONS'],
                'sort_order' => 12,
            ],
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_intro',
                'section_title' => 'Giới thiệu & Giải pháp',
                'field_key' => 'about_heading_2',
                'field_label' => 'Tiêu đề dòng 2 (phải)',
                'field_type' => 'text',
                'value' => ['vi' => 'THIẾT KẾ CHO', 'en' => 'FOR'],
                'sort_order' => 13,
            ],
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_intro',
                'section_title' => 'Giới thiệu & Giải pháp',
                'field_key' => 'about_slogan',
                'field_label' => 'Slogan biến đổi',
                'field_type' => 'text',
                'value' => ['vi' => 'THƯƠNG HIỆU DOANH NGHIỆP', 'en' => 'ENTERPRISE BRANDS'],
                'sort_order' => 13,
            ],
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_workflow',
                'section_title' => 'Quy trình làm việc (Workflow)',
                'field_key' => 'step1_title',
                'field_label' => 'Bước 1: Tên bước',
                'field_type' => 'text',
                'value' => ['vi' => 'Nghiên cứu & Định vị', 'en' => 'Research & Positioning'],
                'sort_order' => 14,
            ],
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_workflow',
                'section_title' => 'Quy trình làm việc (Workflow)',
                'field_key' => 'step2_title',
                'field_label' => 'Bước 2: Tên bước',
                'field_type' => 'text',
                'value' => ['vi' => 'Xây dựng ý tưởng', 'en' => 'Concept Strategy'],
                'sort_order' => 15,
            ],
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_workflow',
                'section_title' => 'Quy trình làm việc (Workflow)',
                'field_key' => 'step3_title',
                'field_label' => 'Bước 3: Tên bước',
                'field_type' => 'text',
                'value' => ['vi' => 'Thiết kế hoàn thiện', 'en' => 'Design & Execution'],
                'sort_order' => 16,
            ],

            // DỰ ÁN (/work)
            [
                'page_slug' => '/work',
                'section_id' => 'work_hero',
                'section_title' => 'Banner Dự án & Bộ lọc',
                'field_key' => 'filter_all',
                'field_label' => 'Nút bộ lọc 1',
                'field_type' => 'text',
                'value' => ['vi' => 'TẤT CẢ DỰ ÁN', 'en' => 'ALL PROJECTS'],
                'sort_order' => 17,
            ],
            [
                'page_slug' => '/work',
                'section_id' => 'work_hero',
                'section_title' => 'Banner Dự án & Bộ lọc',
                'field_key' => 'filter_featured',
                'field_label' => 'Nút bộ lọc 2',
                'field_type' => 'text',
                'value' => ['vi' => 'NỔI BẬT', 'en' => 'FEATURED'],
                'sort_order' => 18,
            ],
            [
                'page_slug' => '/work',
                'section_id' => 'work_hero',
                'section_title' => 'Banner Dự án & Bộ lọc',
                'field_key' => 'filter_identity',
                'field_label' => 'Nút bộ lọc 3',
                'field_type' => 'text',
                'value' => ['vi' => 'NHẬN DIỆN', 'en' => 'IDENTITY'],
                'sort_order' => 19,
            ],
            [
                'page_slug' => '/work',
                'section_id' => 'work_hero',
                'section_title' => 'Banner Dự án & Bộ lọc',
                'field_key' => 'filter_website',
                'field_label' => 'Nút bộ lọc 4',
                'field_type' => 'text',
                'value' => ['vi' => 'WEBSITE', 'en' => 'WEBSITE'],
                'sort_order' => 20,
            ],

            // BÀI VIẾT / TIN TỨC (/news)
            [
                'page_slug' => '/news',
                'section_id' => 'news_header',
                'section_title' => 'Tiêu đề trang Tin tức',
                'field_key' => 'news_title1',
                'field_label' => 'Dòng tiêu đề trái',
                'field_type' => 'text',
                'value' => ['vi' => 'TIN TỨC &', 'en' => 'NEWS &'],
                'sort_order' => 21,
            ],
            [
                'page_slug' => '/news',
                'section_id' => 'news_header',
                'section_title' => 'Tiêu đề trang Tin tức',
                'field_key' => 'news_title2',
                'field_label' => 'Dòng tiêu đề phải',
                'field_type' => 'text',
                'value' => ['vi' => 'GÓC NHÌN', 'en' => 'INSIGHTS'],
                'sort_order' => 22,
            ],
        ];

        foreach ($contents as $c) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => $c['page_slug'],
                    'section_id' => $c['section_id'],
                    'field_key' => $c['field_key']
                ],
                $c
            );
        }

        // 4. Projects (Toàn bộ các dự án mẫu ban đầu từ HTML)
        $projects = [
            [
                'title' => ['vi' => 'Nhận diện thương hiệu Tiên Phong', 'en' => 'Tien Phong Identity'],
                'slug' => 'nhan-dien-tien-phong',
                'category' => ['vi' => 'Brand identity', 'en' => 'Brand identity'],
                'thumbnail' => '/assets/images/project1.png',
                'client' => 'Tien Phong Corp',
                'year' => '2026',
                'description' => ['vi' => 'Thiết kế bộ nhận diện thương hiệu số toàn diện.', 'en' => 'Comprehensive digital brand identity design.'],
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => ['vi' => 'Website Trải nghiệm số Norm', 'en' => 'Norm Digital Experience Website'],
                'slug' => 'website-trai-nghiem-so-norm',
                'category' => ['vi' => 'UI/UX Design', 'en' => 'UI/UX Design'],
                'thumbnail' => '/assets/images/project2.png',
                'client' => 'Norm Studio',
                'year' => '2026',
                'description' => ['vi' => 'Trải nghiệm số và hệ thống tương tác hiện đại.', 'en' => 'Modern digital experience and interactive system.'],
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => ['vi' => 'Chiến lược & Nhận diện Nổi bật', 'en' => 'Featured Brand Identity & Strategy'],
                'slug' => 'chien-luoc-nhan-dien-noi-bat',
                'category' => ['vi' => 'Brand identity - UI/UX - Social Strategy', 'en' => 'Brand identity - UI/UX - Social Strategy'],
                'thumbnail' => '/assets/images/project3.png',
                'client' => 'Heritage JSC',
                'year' => '2025',
                'description' => ['vi' => 'Định vị chiến lược và thiết kế bao bì di sản.', 'en' => 'Strategic positioning and heritage packaging design.'],
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => ['vi' => 'Chiến dịch Social Strategy Nổi bật 2', 'en' => 'Featured Social Strategy 2'],
                'slug' => 'chien-dich-social-strategy-2',
                'category' => ['vi' => 'Social Strategy', 'en' => 'Social Strategy'],
                'thumbnail' => '/assets/images/project4.png',
                'client' => 'Vibe Media',
                'year' => '2025',
                'description' => ['vi' => 'Chiến dịch truyền thông thương hiệu sáng tạo.', 'en' => 'Creative brand communication campaign.'],
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'title' => ['vi' => 'Nền tảng E-commerce Platform', 'en' => 'E-commerce Platform'],
                'slug' => 'nen-tang-ecommerce-platform',
                'category' => ['vi' => 'Web Development', 'en' => 'Web Development'],
                'thumbnail' => '/assets/images/project4.png',
                'client' => 'NextGen Retail',
                'year' => '2025',
                'description' => ['vi' => 'Nền tảng thương mại điện tử chuẩn mực tốc độ cao.', 'en' => 'High-performance standard e-commerce platform.'],
                'is_featured' => false,
                'sort_order' => 5,
            ],
        ];
        foreach ($projects as $p) {
            Project::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 5. Services (4 gói dịch vụ chuẩn từ HTML)
        $services = [
            [
                'number' => '01',
                'title' => ['vi' => 'Thiết kế từ chiến lược', 'en' => 'Strategy-led Design'],
                'description' => [
                    'vi' => "Định vị thương hiệu\nCâu chuyện thương hiệu\nĐặt tên thương hiệu\nBản sắc thương hiệu\nThông điệp thương hiệu",
                    'en' => "Brand positioning\nBrand story\nBrand naming\nBrand identity\nBrand messaging"
                ],
                'content' => [
                    'vi' => [
                        'Định vị thương hiệu',
                        'Câu chuyện thương hiệu',
                        'Đặt tên thương hiệu',
                        'Bản sắc thương hiệu',
                        'Thông điệp thương hiệu',
                    ],
                    'en' => [
                        'Brand positioning',
                        'Brand story',
                        'Brand naming',
                        'Brand identity',
                        'Brand messaging',
                    ],
                ],
                'icon' => '/assets/images/project1.png',
                'sort_order' => 1,
            ],
            [
                'number' => '02',
                'title' => ['vi' => 'Thiết kế nhận diện thương hiệu', 'en' => 'Comprehensive Identity Design'],
                'description' => [
                    'vi' => "Thiết kế logo\nHệ thống nhận diện thương hiệu\nBộ nhận diện ứng dụng\nCẩm nang thương hiệu",
                    'en' => "Logo design\nBrand identity system\nApplication kit\nBrand guidelines"
                ],
                'content' => [
                    'vi' => [
                        'Thiết kế logo',
                        'Hệ thống nhận diện thương hiệu',
                        'Bộ nhận diện ứng dụng',
                        'Cẩm nang thương hiệu',
                    ],
                    'en' => [
                        'Logo design',
                        'Brand identity system',
                        'Application kit',
                        'Brand guidelines',
                    ],
                ],
                'icon' => '/assets/images/project2.png',
                'sort_order' => 2,
            ],
            [
                'number' => '03',
                'title' => ['vi' => 'Thiết kế không gian số', 'en' => 'Digital Experience & UI/UX'],
                'description' => [
                    'vi' => "Thiết kế UI/UX\nThiết kế Website\nỨng dụng di động\nTối ưu trải nghiệm",
                    'en' => "UI/UX design\nBrand website\nMobile app\nInteractive user experience"
                ],
                'content' => [
                    'vi' => [
                        'Thiết kế UI/UX',
                        'Thiết kế Website',
                        'Ứng dụng di động',
                        'Tối ưu trải nghiệm',
                    ],
                    'en' => [
                        'UI/UX design',
                        'Brand website',
                        'Mobile app',
                        'Interactive user experience',
                    ],
                ],
                'icon' => '/assets/images/project3.png',
                'sort_order' => 3,
            ],
            [
                'number' => '04',
                'title' => ['vi' => 'Sản xuất nội dung', 'en' => 'Content Production'],
                'description' => [
                    'vi' => "Chụp ảnh doanh nghiệp\nSản xuất Video quảng cáo\nNội dung truyền thông\nQuản trị Fanpage",
                    'en' => "Corporate photography\nPromotional video production\nMedia content\nFanpage management"
                ],
                'content' => [
                    'vi' => [
                        'Chụp ảnh doanh nghiệp',
                        'Sản xuất Video quảng cáo',
                        'Nội dung truyền thông',
                        'Quản trị Fanpage',
                    ],
                    'en' => [
                        'Corporate photography',
                        'Promotional video production',
                        'Media content',
                        'Fanpage management',
                    ],
                ],
                'icon' => '/assets/images/project4.png',
                'sort_order' => 4,
            ],
        ];
        foreach ($services as $srv) {
            Service::updateOrCreate(['number' => $srv['number']], $srv);
        }

        // 6. Blogs (Các bài viết mẫu từ news.blade.php)
        $blogs = [
            [
                'title' => [
                    'vi' => 'Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công nghiệp sáng tạo',
                    'en' => 'Understanding Design Thinking to Stand Out in the Creative Industry'
                ],
                'slug' => 'hieu-ve-tu-duy-thiet-ke-design-thinking',
                'category' => null,
                'thumbnail' => '/assets/images/news1.png',
                'excerpt' => [
                    'vi' => 'Tư duy thiết kế giúp doanh nghiệp giải quyết các bài toán phức tạp bằng cách lấy người dùng làm trọng tâm.',
                    'en' => 'Design thinking helps businesses solve complex problems by putting users at the center.'
                ],
                'content' => [
                    'vi' => "<p>Design Thinking là một cách tiếp cận vấn đề mang tính chiến lược, lấy con người làm trung tâm và hướng đến việc giải quyết những nhu cầu thực sự của người dùng. Trong bài viết này, chúng ta sẽ cùng tìm hiểu sâu về tư duy thiết kế là gì, vì sao tư duy thiết kế lại trở thành lợi thế cạnh tranh trong ngành công nghiệp sáng tạo, cũng như cách áp dụng phương pháp này để nâng tầm chất lượng dự án và tư duy làm nghề.</p>\n\n<h3>1. Tư duy thiết kế (Design Thinking) là gì?</h3>\n\n<p>Tư duy thiết kế, hay Design Thinking, là một phương pháp tư duy và giải quyết vấn đề lấy con người làm trung tâm. Thay vì bắt đầu từ giải pháp hoặc từ góc nhìn chủ quan của người làm sáng tạo, tư duy thiết kế bắt đầu từ việc thấu hiểu sâu sắc nhu cầu, mong muốn, hành vi và \"nỗi đau\" của người dùng, sau đó mới phát triển ý tưởng và giải pháp phù hợp.</p>\n\n<p>Nói một cách đơn giản, tư duy thiết kế (Design Thinking) là cách tiếp cận vấn đề giống như một nhà thiết kế: quan sát, đặt câu hỏi, thấu cảm, thử nghiệm, sai, sửa và tối ưu liên tục. Phương pháp này không chỉ được sử dụng trong thiết kế đồ họa hay thiết kế sản phẩm, mà còn được ứng dụng rộng rãi trong kinh doanh, marketing, giáo dục, công nghệ và quản trị.</p>\n\n<div class=\"article-image-container my-4\">\n    <img src=\"/assets/images/gotrail.jpg\" alt=\"Gotrail Billboard\" class=\"article-fluid-img img-fluid w-100\" style=\"border-radius: 8px;\">\n</div>\n\n<h3>2. Vì sao tư duy thiết kế là lợi thế cạnh tranh?</h3>\n\n<p>Trong bối cảnh thị trường bão hòa, nơi các sản phẩm và dịch vụ ngày càng tương đồng về tính năng lẫn giá cả, trải nghiệm người dùng và giá trị cảm xúc chính là yếu tố tạo nên sự khác biệt.</p>\n\n<div class=\"article-image-container my-4\">\n    <img src=\"/assets/images/gallery1.jpg\" alt=\"Design Thinking Workshop\" class=\"article-fluid-img img-fluid w-100\" style=\"border-radius: 8px;\">\n</div>\n\n<h3>3. Áp dụng tư duy thiết kế vào dự án sáng tạo</h3>\n\n<p>Quy trình Design Thinking tiêu chuẩn gồm 5 giai đoạn: Thấu cảm (Empathize), Xác định vấn đề (Define), Lên ý tưởng (Ideate), Tạo mẫu thử (Prototype), và Kiểm tra (Test).</p>",
                    'en' => "<p>Design Thinking is a strategic, human-centered approach to problem solving that focuses on addressing real user needs.</p>\n\n<h3>1. What is Design Thinking?</h3>\n\n<p>Design thinking is a human-centric methodology for solving complex problems.</p>\n\n<div class=\"article-image-container my-4\">\n    <img src=\"/assets/images/gotrail.jpg\" alt=\"Gotrail Billboard\" class=\"article-fluid-img img-fluid w-100\" style=\"border-radius: 8px;\">\n</div>\n\n<h3>2. Why Design Thinking is a Competitive Advantage</h3>\n\n<p>In saturated markets where products and services share similar features and pricing, user experience and emotional resonance become the true differentiators.</p>"
                ],
                'published_at' => now()->subDays(5),
                'status' => 'published',
                'is_featured' => true,
            ],
            [
                'title' => [
                    'vi' => 'Tái định vị thương hiệu kỷ nguyên số',
                    'en' => 'Brand Repositioning in the Digital Era'
                ],
                'slug' => 'tai-dinh-vi-thuong-hieu-ky-nguyen-so',
                'category' => null,
                'thumbnail' => '/assets/images/project2.png',
                'excerpt' => [
                    'vi' => 'Các nguyên tắc cốt lõi giúp doanh nghiệp làm mới hình ảnh trong mắt người dùng hiện đại.',
                    'en' => 'Core principles helping businesses refresh their image in the modern consumer mindset.'
                ],
                'published_at' => now()->subDays(12),
                'status' => 'published',
                'is_featured' => true,
            ],
            [
                'title' => [
                    'vi' => 'Thiết kế tối giản và sức hút thương hiệu bền vững',
                    'en' => 'Minimalist Design and Enduring Brand Appeal'
                ],
                'slug' => 'thiet-ke-toi-gian-va-suc-hut-thuong-hieu',
                'category' => null,
                'thumbnail' => '/assets/images/project3.png',
                'excerpt' => [
                    'vi' => 'Tại sao sự tối giản luôn là lựa chọn hàng đầu của các thương hiệu hàng đầu thế giới.',
                    'en' => 'Why minimalism remains the premier choice of world-leading brands.'
                ],
                'published_at' => now()->subDays(18),
                'status' => 'published',
                'is_featured' => true,
            ],
        ];
        foreach ($blogs as $b) {
            Blog::updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 7. Reviews (Trích xuất nguyên vẹn 3 đánh giá từ index.blade.php)
        $reviews = [
            [
                'author' => 'Nguyễn Minh Anh',
                'role' => ['vi' => 'Giám đốc Marketing', 'en' => 'Marketing Director'],
                'content' => [
                    'vi' => 'Chúng tôi tìm đến agency khi thương hiệu đang thiếu sự nhất quán và định hướng rõ ràng. Đội ngũ đã giúp chúng tôi nhìn lại toàn bộ câu chuyện thương hiệu, từ chiến lược đến hệ thống nhận diện. Trong suốt quá trình làm việc, mọi thứ được triển khai rất bài bản và minh bạch. Kết quả cuối cùng không chỉ là một bộ nhận diện đẹp mà còn là một nền tảng thương hiệu rõ ràng để doanh nghiệp phát triển lâu dài. Chúng tôi thật sự hài lòng với sự hợp tác này.',
                    'en' => 'We approached the agency when our brand lacked consistency and clear direction. The team helped us review the entire brand narrative, from strategy to identity system. Throughout the project, everything was executed methodically and transparently.'
                ],
                'avatar' => '/assets/images/avatar.png',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'author' => 'Trần Văn B',
                'role' => ['vi' => 'CEO Tiên Phong', 'en' => 'CEO Tien Phong'],
                'content' => [
                    'vi' => 'Dịch vụ rất chuyên nghiệp. Từ khâu lên ý tưởng đến lúc thực thi đều trơn tru. Chúng tôi đánh giá cao sự sáng tạo và khả năng thấu hiểu thị trường của đội ngũ. Bộ nhận diện mới đã giúp doanh số tăng trưởng rõ rệt trong quý vừa qua.',
                    'en' => 'Very professional service. Seamless from ideation to execution. We highly value the team creative acumen and market understanding.'
                ],
                'avatar' => '/assets/images/project2.png',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'author' => 'Lê Thị C',
                'role' => ['vi' => 'Founder Nem.A', 'en' => 'Founder Nem.A'],
                'content' => [
                    'vi' => 'Một trải nghiệm tuyệt vời. Agency không chỉ làm đẹp mà còn tư vấn chiến lược rất sát thực tế. Rất mong được tiếp tục đồng hành trong các dự án tương lai.',
                    'en' => 'A wonderful experience. The agency delivers not just beautiful designs but very practical strategic consultation.'
                ],
                'avatar' => '/assets/images/project3.png',
                'rating' => 5,
                'sort_order' => 3,
            ],
        ];
        foreach ($reviews as $r) {
            Review::updateOrCreate(['author' => $r['author']], $r);
        }

        // 8. Media
        $mediaFiles = [
            ['name' => 'banner.png', 'file_path' => '/assets/images/banner.png', 'file_type' => 'image', 'file_size' => 1024000],
            ['name' => 'project1.png', 'file_path' => '/assets/images/project1.png', 'file_type' => 'image', 'file_size' => 840000],
            ['name' => 'project2.png', 'file_path' => '/assets/images/project2.png', 'file_type' => 'image', 'file_size' => 760000],
            ['name' => 'project3.png', 'file_path' => '/assets/images/project3.png', 'file_type' => 'image', 'file_size' => 920000],
            ['name' => 'project4.png', 'file_path' => '/assets/images/project4.png', 'file_type' => 'image', 'file_size' => 950000],
            ['name' => 'news1.png', 'file_path' => '/assets/images/news1.png', 'file_type' => 'image', 'file_size' => 640000],
            ['name' => 'norm.glb', 'file_path' => '/storage/uploads/norm.glb', 'file_type' => 'model3d', 'file_size' => 40766352],
        ];
        foreach ($mediaFiles as $mf) {
            Media::updateOrCreate(['name' => $mf['name']], $mf);
        }
    }
}
