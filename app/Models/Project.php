<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'thumbnail',
        'gallery',
        'client',
        'year',
        'description',
        'content',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'json',
        'category' => 'json',
        'gallery' => 'json',
        'description' => 'json',
        'content' => 'json',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_category_project', 'project_id', 'project_category_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getTitle(string $lang = 'vi'): string
    {
        return is_array($this->title) ? ($this->title[$lang] ?? $this->title['vi'] ?? '') : ($this->title ?? '');
    }

    public function getCategory(string $lang = 'vi'): string
    {
        if ($this->relationLoaded('categories') && $this->categories->isNotEmpty()) {
            return $this->categories->map(fn($c) => $c->getName($lang))->join(' • ');
        }
        if (is_array($this->category)) {
            return $this->category[$lang] ?? $this->category['vi'] ?? '';
        }
        return (string) ($this->category ?? '');
    }

    public function getDescription(string $lang = 'vi'): string
    {
        return is_array($this->description) ? ($this->description[$lang] ?? $this->description['vi'] ?? '') : ($this->description ?? '');
    }

    public static function formatImagePath(?string $path, string $default = ''): string
    {
        if (empty($path)) {
            return $default;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset(ltrim($path, '/'));
    }

    public function getHeroImage(): string
    {
        $hero = $this->content['hero_image'] ?? null;
        if (!empty($hero)) {
            return self::formatImagePath($hero);
        }
        if (!empty($this->thumbnail)) {
            return self::formatImagePath($this->thumbnail);
        }
        return asset('assets/images/gotrail.jpg');
    }

    public function getOverviewTitle(string $lang = 'vi'): string
    {
        $title = $this->content['overview_title'] ?? null;
        if (is_array($title)) {
            return $title[$lang] ?? $title['vi'] ?? ($lang === 'en' ? '• Project Overview' : '• Tổng quan về dự án');
        }
        if (is_string($title) && !empty($title)) {
            return $title;
        }
        return $lang === 'en' ? '• Project Overview' : '• Tổng quan về dự án';
    }

    public function getOverviewText(string $lang = 'vi'): string
    {
        $text = $this->content['overview_text'] ?? null;
        if (is_array($text)) {
            $val = $text[$lang] ?? $text['vi'] ?? '';
            if (!empty($val)) {
                return $val;
            }
        } elseif (is_string($text) && !empty($text)) {
            return $text;
        }

        $desc = $this->getDescription($lang);
        if (!empty($desc)) {
            return $desc;
        }

        return "NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.";
    }

    public function getOverviewImage(): string
    {
        $img = $this->content['overview_image'] ?? null;
        if (!empty($img)) {
            return self::formatImagePath($img);
        }
        return asset('assets/images/mockup.png');
    }

    public function getGallery(): array
    {
        if (is_array($this->gallery) && count($this->gallery) > 0) {
            return array_map(fn($item) => self::formatImagePath(is_array($item) ? ($item['url'] ?? $item['path'] ?? '') : $item), $this->gallery);
        }
        return [
            asset('assets/images/work1.jpg'),
            asset('assets/images/work2.jpg'),
            asset('assets/images/work3.jpg'),
            asset('assets/images/work4.jpg'),
            asset('assets/images/work5.jpg'),
            asset('assets/images/work6.jpg'),
            asset('assets/images/work7.jpg'),
        ];
    }

    public function getCredits(): array
    {
        if (isset($this->content['credits']) && is_array($this->content['credits']) && count($this->content['credits']) > 0) {
            return $this->content['credits'];
        }
        return [
            ['role' => 'Account Manager', 'name' => 'Sebastian Degenhart'],
            ['role' => 'Project Manager', 'name' => 'Tijana Ostojić'],
            ['role' => 'Design Lead', 'name' => 'Amadeus Malmin'],
            ['role' => 'Copywriter', 'name' => 'Paula Espinosa V.'],
            ['role' => 'UX Designer', 'name' => 'Bartek Papież'],
            ['role' => 'UX Designer Support', 'name' => 'Marco Rinke'],
            ['role' => 'Developer', 'name' => 'Luis Henrique Bizarro'],
        ];
    }
}

