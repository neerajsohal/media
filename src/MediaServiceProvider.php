<?php

namespace Topdot\Media;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MediaServiceProvider extends ServiceProvider
{
    public $routeFilePath = '/routes/media.php';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(realpath(__DIR__.'/resources/views/'), 'media');
        $this->mergeConfigFrom(__DIR__.'/config.php', 'media');

        $this->setupRoutes($this->app->router);

        if ($this->app->runningInConsole()) {
            $this->publishFiles();            
        }
    }



    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__.$this->routeFilePath;

        // but if there's a file with the same name in routes/backpack, use that one
        if (file_exists(base_path().$this->routeFilePath)) {
            $routeFilePathInUse = base_path().$this->routeFilePath;
        }

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    public function publishFiles()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('media.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/public' => public_path(),
            __DIR__.'/../fonts' => public_path('fonts'),
        ], 'public');

        if (! class_exists('CreateTempMediaTable')) {
            $this->publishes([
                __DIR__.'/database/migrations/create_temp_media_table.php.stub' => database_path('migrations/2021_05_06_064425_create_temp_media_table.php'),
            ], 'migrations');
        }
    }
}