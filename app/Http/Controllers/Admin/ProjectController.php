<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('sort_order')->orderBy('id', 'desc')->get();

        return Inertia::render('Projects', [
            'projects' => $projects,
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
                $description = $itemData['description'] ?? '';
                if (is_string($description)) {
                    $description = ['vi' => $description, 'en' => $description];
                }

                $payload = [
                    'title' => $title,
                    'slug' => $itemData['slug'] ?? ('project-' . ($index + 1)),
                    'category' => $category,
                    'thumbnail' => $itemData['thumbnail'] ?? null,
                    'client' => $itemData['client'] ?? null,
                    'year' => $itemData['year'] ?? date('Y'),
                    'description' => $description,
                    'is_featured' => !empty($itemData['is_featured']),
                    'sort_order' => $index + 1,
                ];

                if ($id && is_numeric($id) && Project::find($id)) {
                    Project::where('id', $id)->update($payload);
                } else {
                    Project::create($payload);
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
