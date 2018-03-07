<?php

namespace Baijunyao\LaravelFineUploader;

use Illuminate\Support\ServiceProvider;

class FineUploaderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 发布静态资源文件
        $this->publishes([
            __DIR__.'/resources/statics' => public_path('statics'),
        ], 'public');

        // 发布 html 组件
        $this->publishes([
            __DIR__.'/resources/views/component' => resource_path('views/component'),
        ]);

        // 加载 migrate 
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
