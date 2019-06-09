<?php

declare(strict_types=1);

namespace App\Library;

use App\Exception\SystemException;
use App\Interfaces\StorageInterface;
use App\Library\StorageAdapter\Aliyun;
use App\Library\StorageAdapter\Ftp;
use App\Library\StorageAdapter\Local;
use App\Library\StorageAdapter\Qiniu;
use App\Library\StorageAdapter\Sftp;
use App\Library\StorageAdapter\Upyun;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 文件存储，仅限图片
 */
class Storage implements StorageInterface
{
    /**
     * 存储名称和适配器类名数组
     *
     * @var array
     */
    protected $adapterMap = [
        'local'  => Local::class,
        'ftp'    => Ftp::class,
        'sftp'   => Sftp::class,
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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $storageType = $container->get('optionService')->storage_type;

        if (!isset($this->adapterMap[$storageType])) {
            throw new SystemException('不存在指定的存储类型：' . $storageType);
        }

        $this->adapter = new $this->adapterMap[$storageType]($container);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $path, array $thumbs): array
    {
        return $this->adapter->get($path, $thumbs);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): bool
    {
        return $this->adapter->write($path, $stream, $thumbs);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $path, array $thumbs): bool
    {
        return $this->adapter->delete($path, $thumbs);
    }
}
