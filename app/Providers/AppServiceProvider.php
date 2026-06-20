<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
        View::composer('components.home.footer', function ($view) {
            $view->with('adminWhatsappUrl', $this->getAdminWhatsappUrl());
        });
    }

    private function getAdminWhatsappUrl(): ?string
    {
        if (! Schema::hasTable('users')) {
            return null;
        }

        $phone = User::query()
            ->where('role', 'admin')
            ->whereNotNull('nomor_hp')
            ->orderBy('id')
            ->value('nomor_hp');

        if (! $phone) {
            return null;
        }

        $normalizedPhone = preg_replace('/\D+/', '', $phone);
        $normalizedPhone = preg_replace('/^08/', '628', $normalizedPhone);
        $normalizedPhone = preg_replace('/^0/', '62', $normalizedPhone);

        if ($normalizedPhone === '') {
            return null;
        }

        $message = rawurlencode('Halo admin, saya ingin menghubungi SIRACAS.');

        return "https://wa.me/{$normalizedPhone}?text={$message}";
    }
}
