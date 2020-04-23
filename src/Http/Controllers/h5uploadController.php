<?php

namespace Encore\h5upload\Http\Controllers;

use Encore\Admin\Layout\Content;
use Encore\h5upload\Interfaces\ThirdPartyUpload;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller;

class h5uploadController extends Controller
{
    const HTTP_CODE = [
        'OK' => 200,
        'ERROR' => 500
    ];

    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(view('h5upload::index'));
    }

    function info()
    {
        $rus = app(ThirdPartyUpload::class)->getSts();
        if (!$rus) {
            $this->response(self::HTTP_CODE['ERROR'], app(ThirdPartyUpload::class)->getErrorMessage());
        }
        $this->response(self::HTTP_CODE['OK'], '', $rus);
    }

    function response($code = self::HTTP_CODE['OK'], $msg = '', $data = [])
    {
        throw new HttpResponseException(response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ]));
    }
}
