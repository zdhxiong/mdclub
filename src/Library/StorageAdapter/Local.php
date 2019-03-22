<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Abstracts\ContainerAbstracts;
use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;

/**
 * 本地文件适配器
 *
 * Class Local
 * @package App\Library\Storage\Adapter
 */
class Local extends ContainerAbstracts implements StorageInterface
{
    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * Local constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->setPathPrefix();
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = $this->container->optionService->storage_local_dir;

        if ($prefix && !in_array(substr($prefix, -1), ['/', '\\'])) {
            $prefix .= '/';
        }

        if (!$prefix) {
            $prefix = __DIR__ . '/../../../public/static/upload/';
        }

        $this->pathPrefix = $prefix;
    }

    /**
     * 获取包含文件路径的文件存储地址
     *
     * @param  string $path
     * @return string
     */
    protected function applyPathPrefix(string $path): string
    {
        return $this->pathPrefix . ltrim($path, '\\/');
    }

    /**
     * 确保指定目录存在，若不存在，则创建指定目录
     *
     * @param string $root
     */
    protected function ensureDirectory(string $root): void
    {
        if (!is_dir($root)) {
            $umask = umask(0);

            if (!@mkdir($root, 0755, true)) {
                $mkdirError = error_get_last();
            }

            umask($umask);

            if (!is_dir($root)) {
                $errorMessage = $mkdirError['message'] ?? '';
                throw new \Exception(sprintf('Impossible to create the root directory "%s". %s', $root, $errorMessage));
            }
        }
    }

    /**
     * 写入文件
     *
     * @param  string $path
     * @param  string $tmp_path
     * @return bool
     */
    public function write(string $path, string $tmp_path): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        return copy($tmp_path, $location);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        return @unlink($location);
    }

    /**
     * 删除多个文件
     *
     * @param  array $paths
     * @return bool
     */
    public function deleteMultiple(array $paths): bool
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }

        return true;
    }
}
