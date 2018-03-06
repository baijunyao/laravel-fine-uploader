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
        // 静态资源文件
        $this->publishes([
            __DIR__.'/resources/statics' => public_path('statics'),
        ], 'public');

        // html组件
        $this->publishes([
            __DIR__.'/resources/views/component' => resource_path('views/component'),
        ]);
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
