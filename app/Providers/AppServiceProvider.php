<?php

namespace App\Providers;

use App\Services\GetFromJson;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('INR', function ($expression) {
            return "<?php echo INR($expression); ?>";
        });

        Blade::directive('PRE', function ($expression) {
            return "<?php PRE($expression); ?>";
        });

        Paginator::useBootstrap();
    }
}
