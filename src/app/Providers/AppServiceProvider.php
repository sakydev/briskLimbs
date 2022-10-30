<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use \Illuminate\Contracts\Cache\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Contracts\Cache\Factory $cache
     * @param Setting                        $settings
     *
     * @return void
     */
    public function boot(Factory $cache, Setting $settings)
    {
        $settings = $cache->remember('settings', 60, function() use ($settings)
        {
            if (Schema::hasTable('settings')) {
                return $settings->pluck('value', 'name')->all();
            }
        });

        config()->set('settings', $settings);
    }
}
