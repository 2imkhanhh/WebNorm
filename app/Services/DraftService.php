<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Review;
use App\Models\Service;
use App\Models\StudioDraft;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DraftService
{
    public static function getDraft(string $module): ?StudioDraft
    {
        if (! Schema::hasTable('studio_drafts')) {
            return null;
        }

        return StudioDraft::where('module', $module)->first();
    }

    public static function hasDraft(string $module): bool
    {
        return self::getDraft($module) !== null;
    }

    public static function getSummary(): array
    {
        $labels = [];

        if (Schema::hasTable('page_contents')) {
            $pageLabels = [
                '/' => 'Trang chủ',
                '/about-us' => 'Về chúng tôi',
                '/work' => 'Trang Dự án',
                '/news' => 'Trang Tin tức',
            ];
            $draftSlugs = PageContent::whereNotNull('draft_value')
                ->distinct()
                ->pluck('page_slug');
            foreach ($draftSlugs as $slug) {
                $labels[] = $pageLabels[$slug] ?? $slug;
            }
        }

        if (Schema::hasTable('studio_drafts')) {
            $moduleLabels = [
                'projects' => 'Dự án',
                'services' => 'Dịch vụ',
                'blogs' => 'Bài viết',
                'reviews' => 'Đánh giá',
                'settings' => 'Cài đặt',
            ];
            $draftModules = StudioDraft::pluck('module');
            foreach ($draftModules as $mod) {
                $labels[] = $moduleLabels[$mod] ?? $mod;
            }
        }

        return [
            'has_any' => ! empty($labels),
            'labels' => array_values(array_unique($labels)),
        ];
    }

    public static function hasAnyDraft(): bool
    {
        return self::getSummary()['has_any'];
    }

    public static function saveDraft(string $module, array $payload): StudioDraft
    {
        return StudioDraft::updateOrCreate(
            ['module' => $module],
            ['payload' => $payload]
        );
    }

    public static function discardDraft(string $module): void
    {
        if (! Schema::hasTable('studio_drafts')) {
            return;
        }

        StudioDraft::where('module', $module)->delete();
    }

    /**
     * Build a collection of hydrated Project models from draft items (including categories relation).
     *
     * @return Collection<int, Project>|null
     */
    public static function getDraftProjects(): ?Collection
    {
        $draft = self::getDraft('projects');
        if (! $draft) {
            return null;
        }

        $items = $draft->payload['items'] ?? [];
        if (! is_array($items)) {
            return collect();
        }

        $allCatIds = [];
        foreach ($items as $item) {
            if (! empty($item['category_ids']) && is_array($item['category_ids'])) {
                foreach ($item['category_ids'] as $cid) {
                    if (is_numeric($cid)) {
                        $allCatIds[] = (int) $cid;
                    }
                }
            }
        }

        $categoriesById = ! empty($allCatIds)
            ? ProjectCategory::whereIn('id', array_values(array_unique($allCatIds)))->get()->keyBy('id')
            : collect();

        $models = [];
        foreach ($items as $index => $item) {
            $project = new Project();
            $catIds = array_values(array_filter(
                array_map('intval', is_array($item['category_ids'] ?? null) ? $item['category_ids'] : []),
                fn (int $cid) => $categoriesById->has($cid)
            ));
            $catModels = collect($catIds)->map(fn (int $cid) => $categoriesById->get($cid))->values();

            $category = $item['category'] ?? ['vi' => '', 'en' => ''];
            if ($catModels->isNotEmpty()) {
                $category = [
                    'vi' => $catModels->map(fn ($c) => $c->getName('vi'))->join(' • '),
                    'en' => $catModels->map(fn ($c) => $c->getName('en'))->join(' • '),
                ];
            }

            $project->forceFill([
                'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : 0,
                'title' => is_string($item['title'] ?? null) ? ['vi' => $item['title'], 'en' => $item['title']] : ($item['title'] ?? []),
                'slug' => $item['slug'] ?? ('project-' . ($index + 1)),
                'category' => $category,
                'thumbnail' => $item['thumbnail'] ?? null,
                'gallery' => is_array($item['gallery'] ?? null) ? $item['gallery'] : [],
                'content' => is_array($item['content'] ?? null) ? $item['content'] : [],
                'client' => $item['client'] ?? null,
                'year' => $item['year'] ?? date('Y'),
                'description' => is_string($item['description'] ?? null)
                    ? ['vi' => $item['description'], 'en' => $item['description']]
                    : ($item['description'] ?? []),
                'is_featured' => ! empty($item['is_featured']),
                'is_active' => array_key_exists('is_active', $item) ? (bool) $item['is_active'] : true,
                'sort_order' => $index + 1,
            ]);
            $project->setRelation('categories', $catModels);
            $models[] = $project;
        }

        return collect($models);
    }

    /**
     * Build a collection of hydrated Service models from draft items.
     *
     * @return Collection<int, Service>|null
     */
    public static function getDraftServices(): ?Collection
    {
        $draft = self::getDraft('services');
        if (! $draft) {
            return null;
        }

        $items = $draft->payload['items'] ?? [];
        if (! is_array($items)) {
            return collect();
        }

        $models = [];
        foreach ($items as $index => $item) {
            $description = $item['description'] ?? '';
            if (is_string($description)) {
                $description = ['vi' => $description, 'en' => $description];
            }

            $content = $item['content'] ?? null;
            if (is_array($description)) {
                $viLines = array_values(array_filter(array_map('trim', explode("\n", (string) ($description['vi'] ?? '')))));
                $enLines = array_values(array_filter(array_map('trim', explode("\n", (string) ($description['en'] ?? '')))));
                if (! empty($viLines) || ! empty($enLines)) {
                    $content = [
                        'vi' => ! empty($viLines) ? $viLines : null,
                        'en' => ! empty($enLines) ? $enLines : null,
                    ];
                }
            }

            $service = new Service();
            $service->forceFill([
                'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : 0,
                'number' => $item['number'] ?? sprintf('%02d', $index + 1),
                'title' => is_string($item['title'] ?? null) ? ['vi' => $item['title'], 'en' => $item['title']] : ($item['title'] ?? []),
                'description' => $description,
                'content' => $content,
                'icon' => $item['icon'] ?? null,
                'is_active' => array_key_exists('is_active', $item) ? (bool) $item['is_active'] : true,
                'sort_order' => $index + 1,
            ]);
            $models[] = $service;
        }

        return collect($models);
    }

    /**
     * Build a collection of hydrated Blog models from draft items.
     *
     * @return Collection<int, Blog>|null
     */
    public static function getDraftBlogs(): ?Collection
    {
        $draft = self::getDraft('blogs');
        if (! $draft) {
            return null;
        }

        $items = $draft->payload['items'] ?? [];
        if (! is_array($items)) {
            return collect();
        }

        $models = [];
        foreach ($items as $index => $item) {
            $publishedAt = null;
            if (! empty($item['published_at'])) {
                try {
                    $publishedAt = Carbon::parse($item['published_at']);
                } catch (\Throwable) {
                    $publishedAt = now();
                }
            }

            $blog = new Blog();
            $blog->forceFill([
                'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : 0,
                'title' => is_string($item['title'] ?? null) ? ['vi' => $item['title'], 'en' => $item['title']] : ($item['title'] ?? []),
                'slug' => $item['slug'] ?? ('news-' . ($index + 1)),
                'category' => is_string($item['category'] ?? null) ? ['vi' => $item['category'], 'en' => $item['category']] : ($item['category'] ?? null),
                'thumbnail' => $item['thumbnail'] ?? null,
                'excerpt' => is_string($item['excerpt'] ?? null) ? ['vi' => $item['excerpt'], 'en' => $item['excerpt']] : ($item['excerpt'] ?? []),
                'content' => is_string($item['content'] ?? null) ? ['vi' => $item['content'], 'en' => $item['content']] : ($item['content'] ?? null),
                'author' => $item['author'] ?? '',
                'author_role' => is_string($item['author_role'] ?? null)
                    ? ['vi' => $item['author_role'], 'en' => $item['author_role']]
                    : ($item['author_role'] ?? null),
                'author_avatar' => $item['author_avatar'] ?? null,
                'status' => $item['status'] ?? 'published',
                'is_featured' => ! empty($item['is_featured']),
                'published_at' => $publishedAt ?? now(),
            ]);
            $models[] = $blog;
        }

        return collect($models);
    }

    /**
     * Build a collection of hydrated Review models from draft items.
     *
     * @return Collection<int, Review>|null
     */
    public static function getDraftReviews(): ?Collection
    {
        $draft = self::getDraft('reviews');
        if (! $draft) {
            return null;
        }

        $items = $draft->payload['items'] ?? [];
        if (! is_array($items)) {
            return collect();
        }

        $models = [];
        foreach ($items as $index => $item) {
            $review = new Review();
            $review->forceFill([
                'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : 0,
                'author' => $item['author'] ?? '',
                'role' => is_string($item['role'] ?? null) ? ['vi' => $item['role'], 'en' => $item['role']] : ($item['role'] ?? []),
                'content' => is_string($item['content'] ?? null) ? ['vi' => $item['content'], 'en' => $item['content']] : ($item['content'] ?? []),
                'avatar' => $item['avatar'] ?? null,
                'rating' => $item['rating'] ?? 5,
                'is_active' => array_key_exists('is_active', $item) ? (bool) $item['is_active'] : true,
                'sort_order' => $index + 1,
            ]);
            $models[] = $review;
        }

        return collect($models);
    }
}

