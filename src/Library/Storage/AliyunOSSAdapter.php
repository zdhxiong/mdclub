<?php

declare(strict_types=1);

namespace App\Library\Storage;

use Psr\Container\ContainerInterface;

/**
 * 阿里云 OSS 适配器
 *
 * Class AliyunOSSAdapter
 * @package App\Library\Storage
 */
class AliyunOSSAdapter extends \Xxtime\Flysystem\Aliyun\OssAdapter
{
    /**
     * AliyunOSSAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options)
    {
        $config = [
            'access_id'      => $options['storage_aliyun_oss_access_id'],
            'access_secret'  => $options['storage_aliyun_oss_access_secret'],
            'bucket'         => $options['storage_aliyun_oss_bucket'],
            'endpoint'       => $options['storage_aliyun_oss_endpoint'],
            'timeout'        => 60,
            'connectTimeout' => 5,
        ];

        parent::__construct($config);
    }
}
