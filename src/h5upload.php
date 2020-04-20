<?php

namespace Encore\h5upload;

use Encore\Admin\Extension;

class h5upload extends Extension
{
    public $name = 'h5upload';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'H5Upload',
        'path'  => 'h5upload',
        'icon'  => 'fa-gears',
    ];
}