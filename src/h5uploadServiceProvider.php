<?php

namespace Encore\h5upload;

use Encore\h5upload\Achieves\Aliyun;
use Encore\Admin\Admin;
use Encore\h5upload\Achieves\Location;
use Encore\h5upload\Interfaces\ThirdPartyUpload;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class h5uploadServiceProvider extends ServiceProvider
{

    /**
     * {@inheritdoc}
     */
    public function boot(h5upload $extension)
    {
        if (!h5upload::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'h5upload');
        }

        if ($migrations = $extension->migrations()) {
            $this->loadMigrationsFrom($migrations);
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $resource = [
                $assets => public_path('vendor/laravel-admin-ext/h5upload'),//静态文件
                $assets . '/../config/' => config_path(''),//配置文件
            ];
            $this->publishes(
                $resource,
                'h5upload'
            );
        }
        Admin::booting(function () {
            $type = config('h5upload.type_dev');
            //这里根据配置来加载js文件
            if ($type == 'ali') {
                Admin::js('vendor/laravel-admin-ext/h5upload/js/h5upload-ali.js?v=' . rand(1, 100));
                Admin::js('vendor/laravel-admin-ext/h5upload/js/ali-oss-sdk/aliyun-oss-sdk.min.js');
            } else if ($type == 'location') {
                Admin::js('vendor/laravel-admin-ext/h5upload/js/h5upload-location.js?v=' . rand(1, 100));
            }
            //配置文件读取css
            $css = config('h5upload.css', ['vendor/laravel-admin-ext/h5upload/css/h5upload.css']);
            foreach ($css as $load) {
                Admin::css($load);
            }
        });
        $this->app->booted(function () {
            h5upload::routes(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * 注册服务提供者
     */
    function register()
    {
        $this->app->bind(ThirdPartyUpload::class, function (Application $application) {
            $type_dev = config('h5upload.type_dev');
            $dev_map = [
                'ali' => Aliyun::class,
                'location' => Location::class
            ];
            return new $dev_map[$type_dev]($type_dev);
        });
    }
}
