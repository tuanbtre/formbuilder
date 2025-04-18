<?php

namespace Tuanbtre\FormBuilder;

use Illuminate\Support\ServiceProvider;

class FormBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Đăng ký routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Đăng ký views
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'Form');

        // Đăng ký migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Publish views để tùy chỉnh
        $this->publishes([
            __DIR__ . '/Resources/views' => resource_path('views/Admin'),
        ], 'form-builder-views');

        // Publish migrations để tùy chỉnh
        $this->publishes([
            __DIR__ . '/Database/Migrations' => database_path('migrations'),
        ], 'form-builder-migrations');
    }

    public function register()
    {
        // Đăng ký lệnh Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Tuanbtre\FormBuilder\Console\Commands\InstallFormBuilder::class,
            ]);
        }
    }
}