<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Services\DraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $categories = ProjectCategory::orderBy('sort_order')->orderBy('id')->get();
        $draft = DraftService::getDraft('projects');

        if ($draft) {
            $categoriesById = $categories->keyBy('id');
            $items = collect($draft->payload['items'] ?? [])->map(function ($item) use ($categoriesById) {
                $catIds = array_values(array_filter(
                    array_map('intval', is_array($item['category_ids'] ?? null) ? $item['category_ids'] : []),
                    fn (int $cid) => $categoriesById->has($cid)
                ));
                $item['category_ids'] = $catIds;
                $item['categories'] = collect($catIds)->map(fn (int $cid) => $categoriesById->get($cid))->values()->all();
                return $item;
            })->values()->all();

            return Inertia::render('Projects', [
                'projects' => $items,
                'categories' => $categories,
                'deletedIds' => $draft->payload['deleted_ids'] ?? [],
                'hasDraft' => true,
                'draftUpdatedAt' => $draft->updated_at?->toIso8601String(),
            ]);
        }

        $projects = Project::with('categories')->orderBy('sort_order')->orderBy('id', 'desc')->get();

        return Inertia::render('Projects', [
            'projects' => $projects,
            'categories' => $categories,
            'deletedIds' => [],
            'hasDraft' => false,
            'draftUpdatedAt' => null,
        ]);
    }

    public function saveDraft(Request $request)
    {
        DraftService::saveDraft('projects', [
            'items' => $request->input('items', []),
            'deleted_ids' => $request->input('deleted_ids', []),
        ]);

        return back()->with('success', 'Đã lưu bản nháp dự án thành công!');
    }

    public function discardDraft()
    {
        DraftService::discardDraft('projects');

        return back()->with('success', 'Đã xóa bản nháp dự án, quay về dữ liệu đã xuất bản!');
    }

    public function publish(Request $request)
    {
        $items = $request->input('items', []);
        $deletedIds = $request->input('deleted_ids', []);

        DB::transaction(function () use ($items, $deletedIds) {
            if (!empty($deletedIds)) {
                Project::whereIn('id', $deletedIds)->delete();
            }
            foreach ($items as $index => $itemData) {
                $id = $itemData['id'] ?? null;

                $title = $itemData['title'] ?? '';
                if (is_string($title)) {
                    $title = ['vi' => $title, 'en' => $title];
                }

                $category = $itemData['category'] ?? '';
                if (is_string($category)) {
                    $category = ['vi' => $category, 'en' => $category];
                }

                $categoryIds = $itemData['category_ids'] ?? [];
                if (!empty($categoryIds)) {
                    $selectedCats = ProjectCategory::whereIn('id', $categoryIds)->get();
                    if ($selectedCats->isNotEmpty()) {
                        $category = [
                            'vi' => $selectedCats->map(fn($c) => $c->getName('vi'))->join(' • '),
                            'en' => $selectedCats->map(fn($c) => $c->getName('en'))->join(' • '),
                        ];
                    }
                }

                $description = $itemData['description'] ?? '';
                if (is_string($description)) {
                    $description = ['vi' => $description, 'en' => $description];
                }

                $gallery = $itemData['gallery'] ?? [];
                if (!is_array($gallery)) {
                    $gallery = [];
                }

                $content = $itemData['content'] ?? [];
                if (!is_array($content)) {
                    $content = [];
                }

                $payload = [
                    'title' => $title,
                    'slug' => $itemData['slug'] ?? ('project-' . ($index + 1)),
                    'category' => $category,
                    'thumbnail' => $itemData['thumbnail'] ?? null,
                    'gallery' => $gallery,
                    'content' => $content,
                    'client' => $itemData['client'] ?? null,
                    'year' => $itemData['year'] ?? date('Y'),
                    'description' => $description,
                    'is_featured' => !empty($itemData['is_featured']),
                    'sort_order' => $index + 1,
                ];

                $project = null;
                if ($id && is_numeric($id) && ($existing = Project::find($id))) {
                    $existing->update($payload);
                    $project = $existing;
                } else {
                    $project = Project::create($payload);
                }

                if ($project && isset($itemData['category_ids'])) {
                    $project->categories()->sync($itemData['category_ids']);
                }
            }
        });

        DraftService::discardDraft('projects');

        return back()->with('success', 'Đã xuất bản danh sách dự án thành công vào cơ sở dữ liệu!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required|string|unique:projects,slug',
            'category' => 'nullable',
            'thumbnail' => 'nullable|string',
            'client' => 'nullable|string',
            'year' => 'nullable|string',
            'description' => 'nullable',
            'is_featured' => 'nullable|boolean',
        ]);

        if (is_string($validated['title'])) {
            $validated['title'] = ['vi' => $validated['title'], 'en' => $validated['title']];
        }
        if (isset($validated['category']) && is_string($validated['category'])) {
            $validated['category'] = ['vi' => $validated['category'], 'en' => $validated['category']];
        }
        if (isset($validated['description']) && is_string($validated['description'])) {
            $validated['description'] = ['vi' => $validated['description'], 'en' => $validated['description']];
        }

        Project::create($validated);

        return back()->with('success', 'Đã thêm dự án thành công!');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required|string|unique:projects,slug,' . $project->id,
            'category' => 'nullable',
            'thumbnail' => 'nullable|string',
            'client' => 'nullable|string',
            'year' => 'nullable|string',
            'description' => 'nullable',
            'is_featured' => 'nullable|boolean',
        ]);

        if (is_string($validated['title'])) {
            $validated['title'] = ['vi' => $validated['title'], 'en' => $project->title['en'] ?? $validated['title']];
        }
        if (isset($validated['category']) && is_string($validated['category'])) {
            $validated['category'] = ['vi' => $validated['category'], 'en' => $project->category['en'] ?? $validated['category']];
        }
        if (isset($validated['description']) && is_string($validated['description'])) {
            $validated['description'] = ['vi' => $validated['description'], 'en' => $project->description['en'] ?? $validated['description']];
        }

        $project->update($validated);

        return back()->with('success', 'Đã cập nhật dự án thành công!');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return back()->with('success', 'Đã xóa dự án thành công!');
    }
}
