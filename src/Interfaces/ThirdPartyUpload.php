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
}