#H5直传阿里云oss扩展

###1.修改composer.json文件的repositories 加入

````
 "h5upload": {
   "type": "path",
   "url": "app/Admin/Extensions/laravel-admin-ext/h5upload"
 }
````

###2.使用composer安装

``composer require laravel-admin-ext/h5upload -vvvv``

###3.导出资源文件

`windows`:`php artisan vendor:publish --provider=Encore\h5upload\h5uploadServiceProvider`

`mac|linux`:`php artisan vendor:publish --provider=Encore\\h5upload\\h5uploadServiceProvider`

###4.在`app/Admin/bootstrap.php`添加代码

```
Encore\Admin\Form::extend('h5upload', \Encore\h5upload\h5uploadFiled::class);
```

###5.在form方法里面使用

``
$form->h5upload('url','视频');
``

###设置允许上传扩展的文件

```
可选扩展:video视频类型文件 file所有类型的文件 mp3音频文件 image图片文件
$form->h5upload('url','视频')->setExpansion('video');
```
