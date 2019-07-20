<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Exception\SystemException;
use MDClub\Library\StorageAdapter\Aliyun;
use MDClub\Library\StorageAdapter\Ftp;
use MDClub\Library\StorageAdapter\Interfaces;
use MDClub\Library\StorageAdapter\Local;
use MDClub\Library\StorageAdapter\Qiniu;
use MDClub\Library\StorageAdapter\Sftp;
use MDClub\Library\StorageAdapter\Upyun;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 文件存储，仅限图片
 */
class Storage implements Interfaces
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
     * @var Interfaces
     */
    protected $adapter;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /** @var Option $option */
        $option = $container->get('option');

        $storageType = $option->storage_type;

        if (!isset($this->adapterMap[$storageType])) {
            throw new SystemException('不存在指定的存储类型：' . $storageType);
        }

        $this->adapter = new $this->adapterMap[$storageType]($container);
    }

    /**
     * @inheritDoc
     */
    public function get(string $path, array $thumbs): array
    {
        return $this->adapter->get($path, $thumbs);
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): void
    {
        $this->adapter->write($path, $stream, $thumbs);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $this->adapter->delete($path, $thumbs);
    }
}
