<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('categories')->orderBy('sort_order')->orderBy('id', 'desc')->get();
        $categories = ProjectCategory::orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Projects', [
            'projects' => $projects,
            'categories' => $categories,
        ]);
    }

    public function publish(Request $request)
    {
        $items = $request->input('items', []);
        $deletedIds = $request->input('deleted_ids', []);

        DB::transaction(function () use ($items, $deletedIds) {
            // Delete items that were removed in the staging UI
            if (!empty($deletedIds)) {
                Project::whereIn('id', $deletedIds)->delete();
            }

            // Sync and save pending items
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

        // Normalize bilingual values
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
