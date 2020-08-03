<?php

namespace Encore\h5upload\Achieves;

use Encore\h5upload\Abstracts\ThirdPartyUploadAbs;
use Encore\h5upload\Interfaces\ThirdPartyUpload;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Location extends ThirdPartyUploadAbs implements ThirdPartyUpload
{

    function checkConfig(array $config)
    {

    }

    function getSts()
    {

    }

    /**
     * 上传文件到本地
     * @param UploadedFile $file 资源文件
     * @return string|null
     */
    function uploadByFile($file): ?string
    {
        if (!$file instanceof UploadedFile) {
            return null;
        }
        $saveFileName = md5($file->getBasename());
        if ($originalName = $file->getClientOriginalExtension()) {
            $saveFileName = $saveFileName . '.' . $originalName;
        }
        try {
            $file->move(config('h5upload.location.location_save_path') . '/' . date('Y-m-d') . '/', $saveFileName);
        } catch (FileException $exception) {
            return null;
        }
        return '/storage/h5upload/upload/' . date('Y-m-d') . '/' . $saveFileName;
    }
}
