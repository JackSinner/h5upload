<?php
return [
    'type_dev' => 'ali',//上传驱动
    'ali' => [
        // 端口设置
        'endpoint' => env('ALIYUN_OSS_ENDPOINT'),
        'bucket' => env('ALIYUN_OSS_BUCKET'),
        'bucket_private' => env('ALIYUN_OSS_BUCKET_PRIVATE'),

        // Access Key
        'access_key' => env('ALIYUN_OSS_ACCESS_KEY'),
        'access_secret' => env('ALIYUN_OSS_ACCESS_SECRET'),

        //sts
        'sts_ram' => env('ALIYUN_STS_RAM'),
        'sts_endpoint' => env('ALIYUN_STS_ENDPOINT'),
        'sts_region_id' => env('ALIYUN_STS_REGION_ID'),

        //域名
        'public_domain' => env('ALIYUN_OSS_PUBLIC_DOMAIN'),
        'private_domain' => env('ALIYUN_OSS_PRIVATE_DOMAIN'),
        'domain' => env('ALIYUN_OSS_DOMAIN'),
        // 移除非必需权限，只保留 Put 文件上传功能，此处可根据需要自行修改
        'policy' => '{
        "Statement": [
            {
                "Action": [
                    "oss:PutObject"
                ],
                "Effect": "Allow",
                "Resource":[
                    "acs:oss:*:*:'.env('ALIYUN_OSS_BUCKET').'/*"
                ]
            }
        ],
        "Version": "1"
        }'
    ]
];
