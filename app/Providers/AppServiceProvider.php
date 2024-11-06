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
        //
    }
}