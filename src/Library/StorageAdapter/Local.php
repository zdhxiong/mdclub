<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Option;
use MDClub\Helper\Url;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 本地文件适配器
 */
class Local extends Abstracts implements Interfaces
{
    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct()
    {
        $this->setPathPrefix();
    }

    /**
     * 获取 Filesystem 实例
     *
     * @return Filesystem
     */
    protected function getFilesystem(): Filesystem
    {
        if (!$this->filesystem) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = Option::get(OptionConstant::STORAGE_LOCAL_DIR);

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
     * @inheritDoc
     */
    public function get(string $path, array $thumbs): array
    {
        $storagePath = Url::storagePath();
        $data['original'] = $storagePath . $path;

        foreach (array_keys($thumbs) as $size) {
            $data[$size] = $storagePath . $this->getThumbLocation($path, $size);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);
        $filesystem = $this->getFilesystem();

        $filesystem->copy((string) $stream->getMetadata('uri'), $location);

        $this->crop(
            $stream,
            $thumbs,
            $location,
            /**
             * @param string $pathTmp      缩略图临时文件路径
             * @param string $cropLocation 缩略图将要保存的路径
             */
            function (string $pathTmp, string $cropLocation) use ($filesystem) {
                $filesystem->copy($pathTmp, $cropLocation);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);
        $filesystem = $this->getFilesystem();

        $filesystem->remove($location);

        foreach (array_keys($thumbs) as $size) {
            $filesystem->remove($this->getThumbLocation($location, $size));
        }
    }
}
