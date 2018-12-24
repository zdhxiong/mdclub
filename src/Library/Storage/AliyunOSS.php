<?php

declare(strict_types=1);

namespace App\Library\Storage;

use App\Library\Storage\Interfaces\StorageDriverInterface;
use Xxtime\Flysystem\Aliyun\OssAdapter;

/**
 * 阿里云 OSS 驱动
 *
 * Class AliyunOSS
 * @package App\Library\Storage
 */
class AliyunOSS implements StorageDriverInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * AliyunOSS constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 获取适配器实例
     *
     * @return OssAdapter
     */
    public function getAdapter(): OssAdapter
    {
        return new OssAdapter([
            'access_id'      => $this->option['storage_aliyun_oss_access_id'],
            'access_secret'  => $this->option['storage_aliyun_oss_access_secret'],
            'bucket'         => $this->option['storage_aliyun_oss_bucket'],
            'endpoint'       => $this->option['storage_aliyun_oss_endpoint'],
            'timeout'        => 60,
            'connectTimeout' => 5,
        ]);
    }
}
