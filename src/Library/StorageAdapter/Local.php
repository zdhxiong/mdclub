<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Exception\SystemException;
use App\Interfaces\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 本地文件适配器
 */
class Local extends AbstractAdapter implements StorageInterface
{
    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->setPathPrefix();
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = $this->optionService->storage_local_dir;

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
        if (is_dir($root)) {
            return;
        }

        if (!@mkdir($root, 0755, true) && !is_dir($root)) {
            $mkdirError = error_get_last();
            $errorMessage = $mkdirError['message'] ?? '';
            throw new SystemException(sprintf('Impossible to create the root directory "%s". %s', $root, $errorMessage));
        }
    }

    /**
     * 获取图片 URL
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return array
     */
    public function get(string $path, array $thumbs): array
    {
        $url = $this->urlService->storage();
        $data['o'] = $url . $path;

        foreach (array_keys($thumbs) as $size) {
            $data[$size] = $url . $this->getThumbLocation($path, $size);
        }

        return $data;
    }

    /**
     * 写入文件
     *
     * @param  string          $path
     * @param  StreamInterface $stream
     * @param  array           $thumbs
     * @return bool
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        copy($stream->getMetadata('uri'), $location);

        $this->crop($stream, $thumbs, $location, static function ($pathTmp, $cropLocation) {
            copy($pathTmp, $cropLocation);
        });

        return true;
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return bool
     */
    public function delete(string $path, array $thumbs): bool
    {
        $location = $this->applyPathPrefix($path);

        @unlink($location);

        foreach (array_keys($thumbs) as $size) {
            @unlink($this->getThumbLocation($location, $size));
        }

        return true;
    }
}
