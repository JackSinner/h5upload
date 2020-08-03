<?php

namespace Encore\h5upload\Interfaces;
/**
 * Interface ThirdPartyUpload
 * 第三方的上传实现类
 * auto:MonsterYuan
 */
interface ThirdPartyUpload
{
    /**
     * 用于检查配置文件是否满足
     * @param array $config 当前驱动的配置数据
     * @return mixed
     */
    function checkConfig(array $config);

    function getSts();

    function setErrorMessage(string $message): bool;

    function getErrorMessage(): string;

    /**
     * 获取资源url,集成的类已实现,如果需要另外实现,请重写
     * @param array $resource 资源id数组
     * @return array 返回资源的url,数据格式是[id=>uri]
     */
    function getResourceUri(array $resource): array;
}
