<?php

declare(strict_types=1);

namespace App\Library\Storage;

use Overtrue\Flysystem\Qiniu\QiniuAdapter;

/**
 * 七牛云存储驱动
 *
 * Class Qiniu
 * @package App\Library\Storage
 */
class Qiniu implements StorageInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Qiniu constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 获取适配器实例
     *
     * @return QiniuAdapter
     */
    public function getAdapter(): QiniuAdapter
    {
        return new QiniuAdapter(
            $this->option['storage_qiniu_access_id'],
            $this->option['storage_qiniu_access_secret'],
            $this->option['storage_qiniu_bucket'],
            $this->option['storage_qiniu_endpoint']
        );
    }
}
