<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function publish(Request $request)
    {
        $items = $request->input('items', []);
        $deletedIds = $request->input('deleted_ids', []);

        DB::transaction(function () use ($items, $deletedIds) {
            if (!empty($deletedIds)) {
                Service::whereIn('id', $deletedIds)->delete();
            }

            foreach ($items as $index => $itemData) {
                $id = $itemData['id'] ?? null;

                $title = $itemData['title'] ?? '';
                if (is_string($title)) {
                    $title = ['vi' => $title, 'en' => $title];
                }
                $description = $itemData['description'] ?? '';
                if (is_string($description)) {
                    $description = ['vi' => $description, 'en' => $description];
                }

                $content = $itemData['content'] ?? null;
                if (!$content && is_array($description)) {
                    $viLines = array_values(array_filter(array_map('trim', explode("\n", $description['vi'] ?? ''))));
                    $enLines = array_values(array_filter(array_map('trim', explode("\n", $description['en'] ?? ''))));
                    if (!empty($viLines) || !empty($enLines)) {
                        $content = [
                            'vi' => !empty($viLines) ? $viLines : null,
                            'en' => !empty($enLines) ? $enLines : null,
                        ];
                    }
                }

                $payload = [
                    'number' => $itemData['number'] ?? sprintf('%02d', $index + 1),
                    'title' => $title,
                    'description' => $description,
                    'content' => $content,
                    'icon' => $itemData['icon'] ?? null,
                    'sort_order' => $index + 1,
                ];

                if ($id && is_numeric($id) && Service::find($id)) {
                    Service::where('id', $id)->update($payload);
                } else {
                    Service::create($payload);
                }
            }
        });

        return back()->with('success', 'Đã xuất bản dịch vụ vào cơ sở dữ liệu thành công!');
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

        if (isset($validated['description']) && is_array($validated['description'])) {
            $viLines = array_values(array_filter(array_map('trim', explode("\n", $validated['description']['vi'] ?? ''))));
            $enLines = array_values(array_filter(array_map('trim', explode("\n", $validated['description']['en'] ?? ''))));
            $validated['content'] = [
                'vi' => !empty($viLines) ? $viLines : null,
                'en' => !empty($enLines) ? $enLines : null,
            ];
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

        if (isset($validated['description']) && is_array($validated['description'])) {
            $viLines = array_values(array_filter(array_map('trim', explode("\n", $validated['description']['vi'] ?? ''))));
            $enLines = array_values(array_filter(array_map('trim', explode("\n", $validated['description']['en'] ?? ''))));
            $validated['content'] = [
                'vi' => !empty($viLines) ? $viLines : null,
                'en' => !empty($enLines) ? $enLines : null,
            ];
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
