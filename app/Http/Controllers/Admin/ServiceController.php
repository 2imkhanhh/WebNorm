<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->orderBy('id', 'asc')->get();

        return Inertia::render('Services', [
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10',
            'title' => 'required',
            'description' => 'nullable',
            'icon' => 'nullable|string',
        ]);

        if (is_string($validated['title'])) {
            $validated['title'] = ['vi' => $validated['title'], 'en' => $validated['title']];
        }
        if (isset($validated['description']) && is_string($validated['description'])) {
            $validated['description'] = ['vi' => $validated['description'], 'en' => $validated['description']];
        }

        Service::create($validated);

        return back()->with('success', 'Đã thêm dịch vụ thành công!');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10',
            'title' => 'required',
            'description' => 'nullable',
            'icon' => 'nullable|string',
        ]);

        if (is_string($validated['title'])) {
            $validated['title'] = ['vi' => $validated['title'], 'en' => $service->title['en'] ?? $validated['title']];
        }
        if (isset($validated['description']) && is_string($validated['description'])) {
            $validated['description'] = ['vi' => $validated['description'], 'en' => $service->description['en'] ?? $validated['description']];
        }

        $service->update($validated);

        return back()->with('success', 'Đã cập nhật dịch vụ thành công!');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with('success', 'Đã xóa dịch vụ thành công!');
    }
}
