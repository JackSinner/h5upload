<?php

namespace Encore\h5upload\Achieves;

use AlibabaCloud\{
    Client\AlibabaCloud,
    Client\Exception\ClientException,
    Client\Exception\ServerException
};
use Encore\h5upload\{Interfaces\ThirdPartyUpload, Abstracts\ThirdPartyUploadAbs, models\ResourceModel};

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
        AlibabaCloud::accessKeyClient($this->config['access_key'], $this->config['access_secret'])
            ->regionId($this->config['sts_region_id'])
            ->verify(false)
            ->asDefaultClient();
        try {
            $response = AlibabaCloud::rpc()
                ->product('Sts')
                ->scheme('https') // https | http
                ->version('2015-04-01')
                ->action('AssumeRole')
                ->method('POST')
                ->host('sts.aliyuncs.com')
                ->verify(false)
                ->options([
                    'query' => [
                        'RegionId' => $this->config['sts_region_id'],
                        'RoleArn' => $this->config['sts_ram'],
                        'RoleSessionName' => "test",
                        'DurationSeconds' => "1000",
                    ]
                ])
                ->request();
            if (!$response->isSuccess()) {
                return $this->setErrorMessage('获取STS授权失败');
            }
        } catch (ClientException $clientException) {
            return $this->setErrorMessage($clientException->getMessage());
        } catch (ServerException $serverException) {
            return $this->setErrorMessage($serverException->getMessage() . $serverException->getErrorCode() . $serverException->getRequestId() . $serverException->getErrorMessage());
        } catch (\Exception $exception) {
            return $this->setErrorMessage($exception->getMessage());
        }
        $response = $response->toArray();
        return [
            'sts' => [
                'SecurityToken' => $response['Credentials']['SecurityToken'],
                'AccessKeySecret' => $response['Credentials']['AccessKeySecret'],
                'AccessKeyId' => $response['Credentials']['AccessKeyId']
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
