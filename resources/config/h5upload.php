<?php
return [
    'type_dev' => env('H5UPLOAD_TYPE_DEV', 'location'),//上传驱动
    'css' => [//如果觉得css不够漂亮,可以修改配置为自己的css文件
        'vendor/laravel-admin-ext/h5upload/css/h5upload.css'
    ],
    'ali' => [//阿里云的配置
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
                    "acs:oss:*:*:' . env('ALIYUN_OSS_BUCKET') . '/*"
                ]
            }
        ],
        "Version": "1"
        }'
    ],
    'location' => [//本地上传配置
        'location_save_path' => storage_path('app/public/' . env('LOCATION_SAVE_PATH', 'h5upload/upload')),
        'public_domain' => env('LOCATION_PUBLIC_DOMAIN', env('APP_URL'))
    ]
];
