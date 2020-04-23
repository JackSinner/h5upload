<?php

namespace Encore\h5upload\Achieves;

use AlibabaCloud\{
    Client\AlibabaCloud,
    Client\Exception\ClientException,
    Client\Exception\ServerException,
    Sts\Sts
};
use Encore\h5upload\{
    Interfaces\ThirdPartyUpload,
    Abstracts\ThirdPartyUploadAbs
};

class Aliyun extends ThirdPartyUploadAbs implements ThirdPartyUpload
{
    /**
     * @var array
     */
    protected $verifyConfig = [
        'endpoint',
        'bucket',
        'bucket_private',
        'access_key',
        'access_secret',
        'sts_ram',
        'sts_endpoint',
        'sts_region_id',
        'public_domain',
        'private_domain',
        'domain',
        'policy'
    ];

    function getSts()
    {
        try {
            AlibabaCloud::accessKeyClient($this->config['access_key'], $this->config['access_secret'])->regionId($this->config['sts_region_id'])->asDefaultClient();
            $response = Sts::v20150401()
                ->assumeRole()//指定角色ARN
                ->withRoleArn($this->config['sts_ram'])
                //RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
                ->withRoleSessionName('h5upload')
                //设置权限策略以进一步限制角色的权限
                //以下权限策略表示拥有所有OSS的只读权限
                ->withPolicy($this->config['policy'])
                ->connectTimeout(60)
                ->timeout(65)
                ->request();
            if (!$response->isSuccess()) {
                return $this->setErrorMessage('获取STS授权失败');
            }
        } catch (ClientException $clientException) {
            return $this->setErrorMessage($clientException->getMessage());
        } catch (ServerException $serverException) {
            return $this->setErrorMessage($serverException->getMessage() . $serverException->getErrorCode() . $serverException->getRequestId() . $serverException->getErrorMessage());
        }
        $response = $response->toArray();
        return [
            'sts' => [
                'SecurityToken' => $response['Credentials']['SecurityToken'],
                'AccessKeySecret' => $response['Credentials']['AccessKeyId'],
                'AccessKeyId' => $response['Credentials']['AccessKeySecret']
            ],
            'oss' => [
                'endpoint' => $this->config['endpoint'],
                'bucket' => $this->config['bucket'],
                'sts_endpoint' => $this->config['sts_endpoint'],
                'sts_region_id' => $this->config['sts_region_id']
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    function checkConfig(array $config)
    {
        foreach ($this->verifyConfig as $k) {
            if (!isset($config[$k]) || empty($config[$k])) {
                return $this->setErrorMessage("请检查配置项,配置项{$k}不正确");
            }
        }
        return true;
    }
}
