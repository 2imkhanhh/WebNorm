<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCategoryController extends Controller
{
    public function store(Request $request)
    {
        $nameVi = trim($request->input('name_vi') ?: $request->input('name_en') ?: '');
        $nameEn = trim($request->input('name_en') ?: $nameVi);

        if (empty($nameVi)) {
            return back()->withErrors(['name_vi' => 'Vui lòng nhập tên danh mục.']);
        }

        $baseSlug = $request->input('slug') ? Str::slug($request->input('slug')) : Str::slug($nameEn ?: $nameVi);
        if (empty($baseSlug)) {
            $baseSlug = 'category-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;
        while (ProjectCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $maxOrder = ProjectCategory::max('sort_order') ?? 0;

        $category = ProjectCategory::create([
            'name' => [
                'vi' => $nameVi,
                'en' => $nameEn,
            ],
            'slug' => $slug,
            'sort_order' => $maxOrder + 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Đã thêm danh mục thành công!',
            ]);
        }

        return back()->with('success', 'Đã thêm danh mục mới thành công!');
    }

    public function update(Request $request, ProjectCategory $category)
    {
        $existingName = is_array($category->name) ? $category->name : ['vi' => $category->name, 'en' => $category->name];

        $nameVi = $request->filled('name_vi') ? trim($request->input('name_vi')) : ($existingName['vi'] ?? '');
        $nameEn = $request->filled('name_en') ? trim($request->input('name_en')) : ($existingName['en'] ?? $nameVi);

        if (empty($nameVi) && empty($nameEn)) {
            return back()->withErrors(['name' => 'Tên danh mục không được để trống.']);
        }
        if (empty($nameVi)) $nameVi = $nameEn;
        if (empty($nameEn)) $nameEn = $nameVi;

        $slug = $request->input('slug') ? Str::slug($request->input('slug')) : $category->slug;

        if ($slug !== $category->slug && ProjectCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $slug . '-' . time();
        }

        $category->update([
            'name' => [
                'vi' => $nameVi,
                'en' => $nameEn,
            ],
            'slug' => $slug,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Đã cập nhật danh mục thành công!',
            ]);
        }

        return back()->with('success', 'Đã cập nhật danh mục thành công!');
    }

    public function destroy(Request $request, ProjectCategory $category)
    {
        $category->projects()->detach();
        $category->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa danh mục thành công!',
            ]);
        }

        return back()->with('success', 'Đã xóa danh mục thành công!');
    }
}

