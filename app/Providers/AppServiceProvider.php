<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\JpesaPayment;
use App\Services\PaymentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the PaymentService interface to the JpesaPayment implementation
        $this->app->singleton(PaymentService::class, function ($app) {
            return new JpesaPayment();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                    view()->share('settings', \App\Models\SystemSetting::first());
                }
            } catch (\Exception $e) {
                // Silently fail to allow the app to boot even if DB is not ready
                \Illuminate\Support\Facades\Log::warning('AppServiceProvider: Could not share system settings: ' . $e->getMessage());
            }
        }
    }
}