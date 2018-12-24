<?php

declare(strict_types=1);

namespace App\Library\Storage;

use App\Library\Storage\Interfaces\StorageDriverInterface;
use League\Flysystem\Adapter\Local as LocalAdapter;

/**
 * 本地存储驱动
 *
 * Class Local
 * @package App\Library\Storage
 */
class Local implements StorageDriverInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Local constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 获取适配器实例
     *
     * @return LocalAdapter
     */
    public function getAdapter(): LocalAdapter
    {
        $uploadDir = $this->option['storage_local_dir'];
        if ($uploadDir && !in_array(substr($uploadDir, -1), ['/', '\\'])) {
            $uploadDir .= '/';
        }

        if (!$uploadDir) {
            $uploadDir = __DIR__ . '/../../../public/static/upload/';
        }

        return new LocalAdapter($uploadDir);
    }
}
