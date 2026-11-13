<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\ProjectCategory;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $identity = ProjectCategory::updateOrCreate(
                ['slug' => 'identity'],
                [
                    'name' => ['vi' => 'Nhận diện', 'en' => 'Identity'],
                    'sort_order' => 1,
                ]
            );

            $website = ProjectCategory::updateOrCreate(
                ['slug' => 'website'],
                [
                    'name' => ['vi' => 'Website', 'en' => 'Website'],
                    'sort_order' => 2,
                ]
            );

            // Remap existing projects from old categories to identity / website
            $projects = Project::with('categories')->get();
            foreach ($projects as $project) {
                $oldSlugs = $project->categories->pluck('slug')->all();
                $rawVi = is_array($project->category) ? ($project->category['vi'] ?? '') : (string) $project->category;
                $rawEn = is_array($project->category) ? ($project->category['en'] ?? '') : (string) $project->category;
                $rawTitle = $project->getTitle('vi') . ' ' . $project->getTitle('en');
                $combined = strtolower($rawVi . ' ' . $rawEn . ' ' . $rawTitle);

                $newIds = [];

                $hasIdentity = !empty(array_intersect($oldSlugs, ['identity', 'brand-identity', 'brand-strategy', 'packaging-identity', 'social-strategy']))
                    || str_contains($combined, 'nhận diện')
                    || str_contains($combined, 'brand')
                    || str_contains($combined, 'identity')
                    || str_contains($combined, 'social')
                    || str_contains($combined, 'bao bì')
                    || str_contains($combined, 'packaging');

                $hasWebsite = !empty(array_intersect($oldSlugs, ['website', 'web-development', 'ui-ux-design']))
                    || str_contains($combined, 'website')
                    || str_contains($combined, 'web')
                    || str_contains($combined, 'e-commerce')
                    || str_contains($combined, 'ui/ux');

                if ($hasIdentity) {
                    $newIds[] = $identity->id;
                }
                if ($hasWebsite) {
                    $newIds[] = $website->id;
                }
                if (empty($newIds)) {
                    $newIds[] = $identity->id;
                }

                $project->categories()->sync($newIds);

                $selectedCats = ProjectCategory::whereIn('id', $newIds)->orderBy('sort_order')->get();
                $project->update([
                    'category' => [
                        'vi' => $selectedCats->map(fn($c) => $c->getName('vi'))->join(' • '),
                        'en' => $selectedCats->map(fn($c) => $c->getName('en'))->join(' • '),
                    ],
                ]);
            }

            // Delete all other categories
            $obsoleteCategories = ProjectCategory::whereNotIn('id', [$identity->id, $website->id])->get();
            foreach ($obsoleteCategories as $cat) {
                $cat->projects()->detach();
                $cat->delete();
            }
        });
    }

    public function down(): void
    {
        //
    }
};

