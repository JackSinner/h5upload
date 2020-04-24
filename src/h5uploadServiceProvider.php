<?php

namespace Encore\h5upload;

use Encore\h5upload\Achieves\Aliyun;
use Encore\Admin\Admin;
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

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [
                    $assets => public_path('vendor/laravel-admin-ext/h5upload'),//静态文件
                    $assets . '/config/' => config_path(''),//配置文件
                ],
                'h5upload'
            );
        }
        Admin::booting(function () {
            Admin::js('vendor/laravel-admin-ext/h5upload/js/lib/md5/md5.js?v=' . rand(1, 100));
            Admin::js('vendor/laravel-admin-ext/h5upload/js/h5upload.js?v=' . rand(1, 100));
            Admin::js('vendor/laravel-admin-ext/h5upload/js/ali-oss-sdk/aliyun-oss-sdk.min.js');
            Admin::css('vendor/laravel-admin-ext/h5upload/css/h5upload.css');
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
                'ali' => Aliyun::class
            ];
            return new $dev_map[$type_dev]($type_dev);
        });
    }
}
