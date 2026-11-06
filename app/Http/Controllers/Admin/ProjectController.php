<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
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
