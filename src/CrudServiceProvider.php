<?php
namespace Bonsai\Crud;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Bonsai\Crud\CrudController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views/', 'bonsaicrud');

        $this->publishes([
            __DIR__ . '/resources/js/' => base_path('/resources/js/views/bonsaicrud'),
        ], 'crud');
    }
}
