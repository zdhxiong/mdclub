<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Storage\AliyunOSS;
use App\Library\Storage\Ftp;
use App\Library\Storage\Local;
use App\Library\Storage\Qiniu;
use App\Library\Storage\Upyun;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;

/**
 * 文件存储，仅限图片
 *
 * Class StorageLibrary
 * @package App\Library
 */
class StorageLibrary
{
    /**
     * 存储驱动实例
     *
     * @var
     */
    protected $driver;

    /**
     * Flysystem 实例
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * StorageLibrary constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        /** @var \App\Service\OptionService $optionService */
        $optionService = $container->get(\App\Service\OptionService::class);
        $option = $optionService->getAll();

        switch ($option['storage_type']) {
            case 'local':
                $this->driver = new Local($option);
                break;

            case 'ftp':
                $this->driver = new Ftp($option);
                break;

            case 'aliyun_oss':
                $this->driver = new AliyunOSS($option);
                break;

            case 'upyun':
                $this->driver = new Upyun($option);
                break;

            case 'qiniu':
                $this->driver = new Qiniu($option);
                break;

            default:
                throw new \Exception('不存在指定的存储类型：' . $option['storage_type']);
        }

        $this->filesystem = new Filesystem($this->driver->getAdapter(), [
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
        ]);
    }

    /**
     * 写入文件
     *
     * @param  string $path
     * @param  string $content
     * @return bool
     */
    public function write(string $path, string $content): bool
    {
        return $this->filesystem->write($path, $content);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return $this->filesystem->delete($path);
    }
}
