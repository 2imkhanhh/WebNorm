<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProjectCategory;
use App\Models\Project;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"vi": "...", "en": "..."}
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('project_category_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_category_id')->constrained('project_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'project_category_id'], 'proj_cat_unique');
        });

        // Seed initial standard categories
        $initialCategories = [
            ['slug' => 'brand-identity', 'name' => ['vi' => 'Nhận diện thương hiệu', 'en' => 'Brand Identity'], 'sort_order' => 1],
            ['slug' => 'ui-ux-design', 'name' => ['vi' => 'Thiết kế UI/UX', 'en' => 'UI/UX Design'], 'sort_order' => 2],
            ['slug' => 'web-development', 'name' => ['vi' => 'Phát triển Website', 'en' => 'Web Development'], 'sort_order' => 3],
            ['slug' => 'brand-strategy', 'name' => ['vi' => 'Chiến lược thương hiệu', 'en' => 'Brand Strategy'], 'sort_order' => 4],
            ['slug' => 'packaging-identity', 'name' => ['vi' => 'Bao bì & Ấn phẩm', 'en' => 'Packaging & Identity'], 'sort_order' => 5],
            ['slug' => 'social-strategy', 'name' => ['vi' => 'Chiến dịch Social', 'en' => 'Social Strategy'], 'sort_order' => 6],
        ];

        foreach ($initialCategories as $cat) {
            ProjectCategory::create($cat);
        }

        // Auto-link existing projects based on their current category string
        $allCats = ProjectCategory::all();
        $projects = Project::all();

        foreach ($projects as $proj) {
            $catVal = $proj->category;
            $viStr = is_array($catVal) ? ($catVal['vi'] ?? '') : (string)$catVal;
            $enStr = is_array($catVal) ? ($catVal['en'] ?? '') : (string)$catVal;
            $combined = strtolower($viStr . ' ' . $enStr);

            $matchedIds = [];
            foreach ($allCats as $c) {
                $cVi = strtolower($c->name['vi'] ?? '');
                $cEn = strtolower($c->name['en'] ?? '');
                $cSlug = strtolower($c->slug);

                if (
                    ($cVi && str_contains($combined, $cVi)) ||
                    ($cEn && str_contains($combined, $cEn)) ||
                    ($cSlug && str_contains($combined, str_replace('-', ' ', $cSlug))) ||
                    ($c->slug === 'brand-identity' && (str_contains($combined, 'brand') || str_contains($combined, 'nhận diện'))) ||
                    ($c->slug === 'ui-ux-design' && (str_contains($combined, 'ui') || str_contains($combined, 'ux'))) ||
                    ($c->slug === 'web-development' && (str_contains($combined, 'web') || str_contains($combined, 'website') || str_contains($combined, 'e-commerce'))) ||
                    ($c->slug === 'social-strategy' && str_contains($combined, 'social')) ||
                    ($c->slug === 'packaging-identity' && (str_contains($combined, 'packaging') || str_contains($combined, 'bao bì')))
                ) {
                    $matchedIds[] = $c->id;
                }
            }

            if (!empty($matchedIds)) {
                $proj->categories()->sync(array_unique($matchedIds));
            } else {
                $firstCat = $allCats->first();
                if ($firstCat) {
                    $proj->categories()->sync([$firstCat->id]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_category_project');
        Schema::dropIfExists('project_categories');
    }
};

