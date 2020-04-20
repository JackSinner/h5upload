<?php

namespace Encore\h5upload\Interfaces;
/**
 * Interface ThirdPartyUpload
 * 第三方的上传实现类
 * auto:MonsterYuan
 */
interface ThirdPartyUpload
{
    function getSts();

    function setErrorMessage(string $message): bool;

    function getErrorMessage(): string;
}

abstract class ThirdPartyUploadAbs
{
    protected $error_message = '';

    function setErrorMessage(string $message): bool
    {
        $this->error_message = $message;
        return false;
    }

    function getErrorMessage(): string
    {
        return $this->error_message;
    }

    function post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    function get($url)
    {
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 超时设置,以秒为单位
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //执行命令
        $data = curl_exec($curl);
        // 显示错误信息
        if (curl_error($curl)) {
            print "Error: " . curl_error($curl);
        } else {
            curl_close($curl);
        }
        return $data;
    }
}
