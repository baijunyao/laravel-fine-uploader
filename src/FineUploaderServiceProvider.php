<?php

namespace Baijunyao\LaravelFineUploader;

use Baijunyao\LaravelFineUploader\Middleware\LaravelFineUploader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;

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
            __DIR__.'/resources/views' => resource_path('views/vendor/laravel-fine-uploader'),
        ]);

        // 发布配置项
        $this->publishes([
            __DIR__.'/config/laravel-fine-uploader.php' => config_path('laravel-fine-uploader.php'),
        ]);

        Blade::directive('fineUploader', function ($expression) {
            return "<?php echo '<span class=\"laravel-fine-uploader-tag bjy-'. uniqid() .'\" style=\"display: none\">' . json_encode($expression) . '</span>' ?>";
        });

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
