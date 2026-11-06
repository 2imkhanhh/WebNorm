<?php

namespace App\Providers;

use App\Helpers\NormHelper;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade directives for dynamic multilingual values
        Blade::directive('normVal', function ($expression) {
            return "<?php echo \App\Helpers\NormHelper::val({$expression}); ?>";
        });

        Blade::directive('normSetting', function ($expression) {
            return "<?php echo \App\Helpers\NormHelper::setting({$expression}); ?>";
        });

        // Share settings & services to all Blade views
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $rawSettings = Setting::all();
                    $settings = [];
                    foreach ($rawSettings as $s) {
                        $settings[$s->key] = $s->value;
                    }
                    $view->with('settings', $settings);
                }
                if (Schema::hasTable('services')) {
                    $globalServices = Service::where('is_active', true)->orderBy('sort_order')->get();
                    $view->with('globalServices', $globalServices);
                }
            } catch (\Throwable $e) {
                // Ignore during cli commands / migrations
            }
        });
    }
}
