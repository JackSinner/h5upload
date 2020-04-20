<?php

namespace Encore\h5upload\lib\ali;

use Sts\Request\V20150401 as Sts;

class StsRole
{
    private $configOss;

    public function __construct($config)
    {
        $this->configOss = $config;
    }

    public function regionId()
    {
        return $this->configOss['sts_region_id'];
    }

    public function stsEndpoint()
    {
        return $this->configOss['sts_endpoint'];
    }

    public function accessKey()
    {
        return $this->configOss['access_key'];
    }

    public function accessSecret()
    {
        return $this->configOss['access_secret'];
    }

    public function stsRam()
    {
        return $this->configOss['sts_ram'];
    }

    function getAssumeRole($userId, $type = 'public')
    {
        include_once 'aliyun-php-sdk-core/Config.php';
        // 只允许子用户使用角色
        $region_id = $this->configOss['sts_region_id'];
        $endpoint = $this->configOss['sts_endpoint'];
        \DefaultProfile::addEndpoint($region_id, $region_id, "Sts", $endpoint);
        $iClientProfile = \DefaultProfile::getProfile($region_id, $this->configOss['access_key'], $this->configOss['access_secret']);
        $client = new \DefaultAcsClient($iClientProfile);
        // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        $roleArn = $this->configOss['sts_ram'];
        $bucket = $this->configOss['bucket'];
        if ($type == 'private') {
            $bucket = $this->configOss['bucket_private'];
        }
        // 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
        // 详情请参考《RAM使用指南》
        // 此授权策略表示读取所有OSS的读写权限
        $policy = <<<POLICY
        {
        "Statement": [
            {
                "Action": [
                    "oss:GetBucketAcl",
                    "oss:ListObjects"
                ],
                "Effect": "Allow",
                "Resource": [
                    "acs:oss:*:*:$bucket/*"
                ]
            },
            {
                "Action": [
                    "oss:PutObject"
                ],
                "Effect": "Allow",
                "Resource":[
                    "acs:oss:*:*:$bucket/*"
                ]
            }
        ],
        "Version": "1"
        }
POLICY;
        $request = new Sts\AssumeRoleRequest();
        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName("client_name" . $userId);
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds(3600);//有效期(过期时间)15-60分钟
        try {
            $response = $client->getAcsResponse($request);
            return $response;
        } catch (ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        } catch (ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }
    }

    function getReadAssumeRole($userId, $type = 'public')
    {
        include_once 'aliyun-php-sdk-core/Config.php';
        // 只允许子用户使用角色
        $region_id = $this->configOss['sts_region_id'];
        $endpoint = $this->configOss['sts_endpoint'];
        \DefaultProfile::addEndpoint($region_id, $region_id, "Sts", $endpoint);
        $iClientProfile = \DefaultProfile::getProfile($region_id, $this->configOss['access_key'], $this->configOss['access_secret']);
        $client = new \DefaultAcsClient($iClientProfile);
        // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        $roleArn = $this->configOss['sts_ram'];
        $bucket = $this->configOss['bucket'];
        if ($type == 'private') {
            $bucket = $this->configOss['bucket_private'];
        }
        $policy = <<<POLICY
        {
        "Statement": [
            {
                "Action": [
                    "oss:GetBucketAcl"
                ],
                "Effect": "Allow",
                "Resource": [
                    "acs:oss:*:*:$bucket/*"
                ]
            },
            {
                "Action": [
                    "oss:GetObject"
                ],
                "Effect": "Allow",
                "Resource":[
                    "acs:oss:*:*:$bucket/*"
                ]
            }
        ],
        "Version": "1"
        }
POLICY;
        $request = new Sts\AssumeRoleRequest();
        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName("client_name" . $userId);
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds(3600);//有效期(过期时间)15-60分钟
        try {
            $response = $client->getAcsResponse($request);
            return $response;
        } catch (ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        } catch (ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }
    }

}
