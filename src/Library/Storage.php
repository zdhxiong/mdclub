<?php

declare(strict_types=1);

namespace App\Library;

use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;
use App\Library\StorageAdapter\Aliyun;
use App\Library\StorageAdapter\Ftp;
use App\Library\StorageAdapter\Local;
use App\Library\StorageAdapter\Qiniu;
use App\Library\StorageAdapter\Upyun;

/**
 * 文件存储，仅限图片
 *
 * Class Storage
 * @package App\Library
 */
class Storage
{
    /**
     * 存储名称和适配器类名数组
     *
     * @var array
     */
    protected $adapterMap = [
        'local'  => Local::class,
        'ftp'    => Ftp::class,
        'aliyun' => Aliyun::class,
        'upyun'  => Upyun::class,
        'qiniu'  => Qiniu::class,
    ];

    /**
     * 存储适配器实例
     *
     * @var StorageInterface
     */
    protected $adapter;

    /**
     * Storage constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $storageType = $container->optionService->storage_type;

        if (!isset($this->adapterMap[$storageType])) {
            throw new \Exception('不存在指定的存储类型：' . $storageType);
        }

        $this->adapter = new $this->adapterMap[$storageType]($container);
    }

    public function write(string $path, string $content): bool
    {
        return $this->adapter->write($path, $content);
    }

    public function delete(string $path): bool
    {
        return $this->adapter->delete($path);
    }

    public function deleteMultiple(array $paths): bool
    {
        return $this->adapter->deleteMultiple($paths);
    }
}
