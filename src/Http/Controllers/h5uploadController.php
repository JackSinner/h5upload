<?php

namespace Encore\h5upload\Http\Controllers;

use Encore\Admin\Layout\Content;
use Encore\h5upload\Interfaces\ThirdPartyUpload;
use Illuminate\Routing\Controller;

class h5uploadController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(view('h5upload::index'));
    }

    function info()
    {
        $sts = app(ThirdPartyUpload::class)->getSts();
        return response()->json([
            'code' => 200,
            'data' => $sts
        ]);
    }
}
