<?php

namespace Encore\h5upload\lib\ali;


use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Http\RequestCore;
use OSS\Http\ResponseCore;

class Signature
{

    private $configOss;
    private $stsRole;

    public function __construct($config)
    {
        $this->configOss = $config;
        $this->stsRole = new StsRole($config);
    }

    //获取临时用户，和权限
    public function getRole($type = 'public')
    {
        // $cache_key = 'signuser-put-ids-'.$type.$user->id;
        // $role = Cache::get($cache_key);//缓存临时用户
        // if(is_null($role)){
        //生成临时权限token
        $role = $this->stsRole->getAssumeRole('h5upload', $type);
        //     $expiration = Carbon::parse($role->Credentials->Expiration);
        //     $role->Credentials->Expiration = $expiration->timestamp;
        //     $role->Credentials->expiration = $expiration->toDateTimeString();
        //     Cache::put($cache_key, $role, $expiration);
        // }
        return $role->Credentials;
    }

    //获取临时用户，和权限
    public function getReadRole($user, $type = 'public')
    {
        // $cache_key = 'signuser-read-ids-'.$type.$user->id;
        // $role = Cache::get($cache_key);//缓存临时用户
        // if(is_null($role)){
        //生成临时权限token
        $role = $this->stsRole->getReadAssumeRole($user->id, $type);
        //     $expiration = Carbon::parse($role->Credentials->Expiration);
        //     $role->Credentials->Expiration = $expiration->timestamp;
        //     $role->Credentials->expiration = $expiration->toDateTimeString();
        //     Cache::put($cache_key, $role, $expiration);
        // }
        return $role->Credentials;
    }

    //生成上传的签名URL
    public function signUrl($user, $object, $timeout = 3600, $type = 'public')
    {
        $credential = $this->getRole($user, $type);
        $accessKeyId = $credential->AccessKeyId;
        $accessKeySecret = $credential->AccessKeySecret;
        // Endpoint
        $endpoint = $this->configOss['endpoint'];
        $bucket = $this->configOss['bucket'];
        if ($type == 'private') {
            $bucket = $this->configOss['bucket_private'];
        }
        $securityToken = $credential->SecurityToken;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $securityToken);
            // 生成PutObject的签名URL。
            $signedUrl = $ossClient->signUrl($bucket, $object, $timeout, "PUT");
        } catch (OssException $e) {
            throw $e;
        }
        return $signedUrl;
    }

    //生成下载签名URL
    public function downloadUrl($user, $object, $timeout = 3600, $type = 'public')
    {
        $credential = $this->getReadRole($user, $type);
        $accessKeyId = $credential->AccessKeyId;
        $accessKeySecret = $credential->AccessKeySecret;
        // Endpoint
        $endpoint = $this->configOss['endpoint'];
        $bucket = $this->configOss['bucket'];
        if ($type == 'private') {
            $bucket = $this->configOss['bucket_private'];
        }
        $securityToken = $credential->SecurityToken;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $securityToken);
            // 生成GetObject的签名URL。
            $ossClient->setUseSSL(false);
            $signedUrl = $ossClient->signUrl($bucket, $object, $timeout);
        } catch (OssException $e) {
            throw $e;
        }
        return $signedUrl;
    }

    //下载文件
    public function getObject($localfile, $object)
    {
        // 指定文件下载路径。localfile
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile
        );

        try {
            $ossClient = new OssClient($this->configOss['access_key'], $this->configOss['access_secret'], $this->configOss['endpoint']);

            $ossClient->getObject($this->configOss['bucket'], $object, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }

    //文件是否存在
    public function doesObjectExist($object)
    {
        try {
            $ossClient = new OssClient($this->configOss['access_key'], $this->configOss['access_secret'], $this->configOss['endpoint']);

            $exist = $ossClient->doesObjectExist($this->configOss['bucket'], $object);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        return $exist;
    }

    //生成上传的签名Sts
    public function signSts($type = 'public')
    {
        $credential = $this->getRole($type);
        return $credential;
    }

    //ossConfig
    public function getConfigOss()
    {
        return $this->configOss;
    }

    //删除单个文件
    public function deleteObject($object)
    {
        try {
            $ossClient = new OssClient($this->configOss['access_key'], $this->configOss['access_secret'], $this->configOss['endpoint']);

            $ossClient->deleteObject($this->configOss['bucket'], $object);
        } catch (OssException $e) {
            throw $e;
        }
    }

    //删除多个文件
    public function deleteObjects($objects)
    {
        try {
            $ossClient = new OssClient($this->configOss['access_key'], $this->configOss['access_secret'], $this->configOss['endpoint']);

            $ossClient->deleteObjects($this->configOss['bucket'], $objects);
        } catch (OssException $e) {
            throw $e;
        }
    }

    //通过URL授权上传文件
    public function putObjectWithUrl($signedUrl, $content)
    {
        $request = new RequestCore($signedUrl);
        // 生成的URL以PUT方式访问。
        $request->set_method('PUT');
        $request->add_header('Content-Type', '');
        $request->add_header('Content-Length', strlen($content));
        $request->set_body($content);
        $request->send_request();
        $res = new ResponseCore($request->get_response_header(),
            $request->get_response_body(), $request->get_response_code());
        if ($res->isOK()) {
            //print(__FUNCTION__ . ": OK" . "\n");
        } else {
            print(__FUNCTION__ . ": FAILED" . "\n");
        }
    }

    //ram直接上传（不需token验证）
    public function putObjectRam($object, $content)
    {
        $accessKeyId = $this->configOss['access_key'];
        $accessKeySecret = $this->configOss['access_secret'];
        // Endpoint
        $endpoint = $this->configOss['endpoint'];
        $bucket = $this->configOss['bucket'];
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $ossClient->putObject($bucket, $object, $content);
        } catch (OssException $e) {
            throw $e;
        }
    }

    //STS临时授权上传文件
    public function putObject($credential, $content, $object)
    {
        $accessKeyId = $credential->AccessKeyId;
        $accessKeySecret = $credential->AccessKeySecret;
        // Endpoint
        $endpoint = $this->configOss['endpoint'];
        $bucket = $this->configOss['bucket'];
        $securityToken = $credential->SecurityToken;

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $securityToken);

            $ossClient->putObject($bucket, $object, $content);
        } catch (OssException $e) {
            throw $e;
        }
    }

}
