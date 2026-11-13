<?php

namespace App\Models;

use App\Helpers\NormHelper;
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

    public function getTitle(?string $lang = null): string
    {
        return NormHelper::val($this->title, '', $lang);
    }

    public function getSlogan(?string $lang = null): string
    {
        $resolvedLang = NormHelper::locale($lang);
        $slogan = $this->content['slogan'] ?? null;
        $val = NormHelper::val($slogan, '', $resolvedLang);
        if (!empty($val)) {
            return $val;
        }
        return $this->getTitle($resolvedLang);
    }

    public function getCategory(?string $lang = null): string
    {
        $resolvedLang = NormHelper::locale($lang);
        if ($this->relationLoaded('categories') && $this->categories->isNotEmpty()) {
            return $this->categories->map(fn($c) => $c->getName($resolvedLang))->join(' • ');
        }
        return NormHelper::val($this->category, '', $resolvedLang);
    }

    public function getDescription(?string $lang = null): string
    {
        return NormHelper::val($this->description, '', $lang);
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

    public function getOverviewTitle(?string $lang = null): string
    {
        $resolvedLang = NormHelper::locale($lang);
        $fallback = $resolvedLang === 'en' ? 'Project Overview' : 'Tổng quan về dự án';
        $title = $this->content['overview_title'] ?? null;
        $val = NormHelper::val($title, $fallback, $resolvedLang);
        $val = preg_replace('/^[•\s\-\*]+/u', '', trim($val));
        return '• ' . (empty($val) ? $fallback : $val);
    }

    public function getOverviewText(?string $lang = null): string
    {
        $resolvedLang = NormHelper::locale($lang);
        $text = $this->content['overview_text'] ?? null;
        $val = NormHelper::val($text, '', $resolvedLang);
        if (!empty($val)) {
            return $val;
        }

        $desc = $this->getDescription($resolvedLang);
        if (!empty($desc)) {
            return $desc;
        }

        return $resolvedLang === 'en'
            ? 'NÔRM Creative is a next-generation creative agency driven by fresh, modern, youthful energy. Built around culture and open design thinking, we bring each brand story and personality to life across every deliverable.'
            : 'NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.';
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

