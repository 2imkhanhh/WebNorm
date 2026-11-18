<?php

namespace App\Providers;

use App\Helpers\NormHelper;
use App\Models\Service;
use App\Models\Setting;
use App\Services\DraftService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

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
                $isPreview = NormHelper::isDraftPreview();

                if (Schema::hasTable('settings')) {
                    $rawSettings = Setting::all();
                    $settings = [];
                    foreach ($rawSettings as $s) {
                        $settings[$s->key] = $s->value;
                    }
                    if ($isPreview && ($draftSettings = DraftService::getDraft('settings'))) {
                        $draftItems = $draftSettings->payload['settings'] ?? [];
                        foreach ($draftItems as $k => $v) {
                            if ($k === 'mail_password_encrypted' || $k === 'mail_password') {
                                continue;
                            }
                            $settings[$k] = $v;
                        }
                    }
                    $view->with('settings', $settings);
                }
                if (Schema::hasTable('services')) {
                    $draftServices = $isPreview ? DraftService::getDraftServices() : null;
                    $globalServices = $draftServices !== null
                        ? $draftServices->filter(fn ($s) => $s->is_active)->values()
                        : Service::where('is_active', true)->orderBy('sort_order')->get();
                    $view->with('globalServices', $globalServices);
                }
            } catch (\Throwable $e) {
                // Ignore during cli commands / migrations
            }
        });
    }
}
