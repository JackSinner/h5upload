<?php

namespace Encore\h5upload\Http\Controllers;

use Encore\Admin\Layout\Content;
use Encore\h5upload\Interfaces\ThirdPartyUpload;
use Encore\h5upload\models\PathModel;
use Encore\h5upload\models\ResourceModel;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
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
        $ThirdPartyUpload = app(ThirdPartyUpload::class);
        $rus = $ThirdPartyUpload->getSts();
        if (!$rus) {
            $this->response(self::HTTP_CODE['ERROR'], $ThirdPartyUpload->getErrorMessage());
        }
        $this->response(self::HTTP_CODE['OK'], '', $rus);
    }

    function saved(Request $request)
    {
        $path = explode('/', $request->post('path'));
        $id = $this->savePath($path);
        //保存到资源库
        $resource = ResourceModel::create([
            'key' => $request->post('key'),
            'path_id' => $id,
            'size' => $request->post('size')
        ]);
        if ($resource->save()) {
            $this->response(self::HTTP_CODE['OK'], '保存成功', [
                'resource_id' => $resource->id
            ]);
        }
        $this->response(self::HTTP_CODE['ERROR'], '保存资源库失败了');
    }

    function savePath(array $path, int $pid = 0)
    {
        foreach ($path as $k => &$p) {
            if (!empty($p)) {
                $pathModel = PathModel::where(['title' => $p, 'pid' => $pid])->first();
                if (!$pathModel) {
                    $pathModel = PathModel::create([
                        'title' => $p,
                        'pid' => $pid
                    ]);
                    if ($pathModel->save()) {
                        $pid = $pathModel->id;
                    }
                    unset($path[$k]);
                    if (count($path) > 0) {
                        return $this->savePath($path, $pid);
                    } else {
                        return $pid;
                    }
                } else {
                    unset($path[$k]);
                    if (count($path) > 1) {
                        return $this->savePath($path, $pathModel->id);
                    } else {
                        return $pathModel->id;
                    }
                }
            } else {
                unset($path[$k]);
            }
        }
    }

    function response($code = self::HTTP_CODE['OK'], $msg = '', $data = [])
    {
        throw new HttpResponseException(response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ]));
    }

    function manage(Content $content)
    {
        //查询树形目录
        $pathModel = PathModel::orderBy('id', 'asc')->get();
        if ($pathModel) {
            $pathModel = $pathModel->toArray();
            $path = [];
            foreach ($pathModel as $p) {
                $path[$p['id']] = $p;
            }
            $pathModel = $path;
        }
        //更改树形结构
        $pathFun = function (&$pathModel) use (&$pathFun) {
            $new = [];
            foreach ($pathModel as $key => &$path) {
                if ($path['pid'] != 0) {
                    if (isset($pathModel[$path['pid']])) {
                        $pathModel[$path['pid']]['son'][$path['id']] = &$path;
                    }
                } else {
                    $new[$path['id']] = &$path;
                }
            }
            return $new;
        };
        $pathModel = $pathFun($pathModel);
        return $content
            ->title('h5upload资源管理')
            ->description('管理资源')
            ->body(view('h5upload::manage', [
                'tree' => $pathModel
            ]));
    }

    function treeInfo(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->get('id');
            $resource = ResourceModel::where(['path_id' => $id])->get();
            return view('h5upload::info', [
                'resource' => $resource,
                'url' => config('h5upload.' . config('h5upload.type_dev') . '.public_domain')
            ]);
        }
    }

    function locationUpload(Request $request, ThirdPartyUpload $h5upload)
    {
        $files = $request->file('files');
        $rus = [];
        foreach ($files as $file) {
            if (!($saveName = $h5upload->uploadByFile($file))) {
                $this->response(self::HTTP_CODE['ERROR'], "文件{$file->getClientOriginalName()}上传失败");
            }
            $rus[] = [
                'save_name' => $saveName,
                'file_size' => $file->getSize()
            ];
        }
        $this->response(self::HTTP_CODE['OK'], '上传成功', $rus);
    }
}
