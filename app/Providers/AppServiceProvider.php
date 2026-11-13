<?php

namespace App\Providers;

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
        foreach (['created', 'updated', 'deleted'] as $evento) {
            \Illuminate\Support\Facades\Event::listen('eloquent.'.$evento.': *',
                function (string $nombre, array $datos) use ($evento) {
                    app(\App\Services\AuditoriaSistema::class)->cambio($evento, $datos[0]);
                });
        }
    }
}
