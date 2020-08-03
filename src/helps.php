<?php

if (!function_exists('getResourceByIds')) {
    /**
     * 获取数据库保存的资源数据
     * @param array $resourceIds 资源id数组,如果不是数字类型会自动剔除掉
     * @return array 返回已存在的资源数据
     */
    function getResourceByIds(array $resourceIds): array
    {
        $resourceIds = collect($resourceIds)->filter(function ($value, $key) {
            return is_numeric($value);
        })->toArray();
        $h5upload = h5upload();
        return $h5upload->getResourceUri($resourceIds);
    }
}

if (!function_exists('h5upload')) {
    /**
     * 获取h5upload实例
     * @return \Encore\h5upload\Interfaces\ThirdPartyUpload
     */
    function h5upload(): \Encore\h5upload\Interfaces\ThirdPartyUpload
    {
        return app(\Encore\h5upload\Interfaces\ThirdPartyUpload::class);
    }
}
