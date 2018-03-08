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

        // 发布 前端页面 组件
        $this->publishes([
            __DIR__.'/resources/views/component' => resource_path('views/component'),
        ]);

        // 发布配置项
        $this->publishes([
            __DIR__.'/config/fineUploader.php' => config_path('fineUploader.php'),
        ]);

        // 加载 表迁移文件
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        // 加载 路由
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
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
