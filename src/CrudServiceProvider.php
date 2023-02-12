<?php
namespace Bonsai\Crud;

use Bonsai\Crud\CrudController;
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
        // $this->app->make('Bonsai\Crud\CrudController');
        $this->app->singleton(CrudController::class, function () {
            return new CrudController();
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // dd('a');
        $this->loadViewsFrom(__DIR__ . '/resources/views/', 'bonsaicrud');

        // $this->publishes([
        //     __DIR__ . '/resources/js/' => base_path('/resources/js/views/bonsaicrud'),
        // ], 'crud');
    }
}
