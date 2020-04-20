<?php

namespace Encore\h5upload\Achieves;

use Encore\h5upload\lib\ali\Signature;
use Encore\h5upload\Interfaces\{
    ThirdPartyUpload,
    ThirdPartyUploadAbs
};

class Aliyun extends ThirdPartyUploadAbs implements ThirdPartyUpload
{
    function getSts()
    {
        $signature = new Signature(config('h5upload.ali'));
        $sigSts = $signature->signSts();
        $configOss = $signature->getConfigOss();
        $oss = [
            'endpoint' => $configOss['endpoint'],
            'bucket' => $configOss['bucket'],
            'sts_endpoint' => $configOss['sts_endpoint'],
            'sts_region_id' => $configOss['sts_region_id'],
        ];
        return [
            'sts' => $sigSts,
            'oss' => $oss,
        ];
    }
}
