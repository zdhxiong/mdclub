<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use Buzz\Browser;
use Buzz\Client\Curl;
use Intervention\Image\ImageManagerStatic;
use MDClub\Facade\Library\Option;
use MDClub\Helper\Str;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;

/**
 * 存储适配器抽象类
 */
abstract class Abstracts
{
    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @var Browser
     */
    private $browser;

    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * 设置文件存储路径前缀
     *
     * @param string $dirConstantName 目录的常量名
     * @param string $defaultDir      默认目录
     */
    protected function setPathPrefix(string $dirConstantName, string $defaultDir = ''): void
    {
        $prefix = Option::get($dirConstantName);

        if ($prefix && !in_array(substr($prefix, -1), ['/', '\\'])) {
            $prefix .= '/';
        }

        if (!$prefix) {
            $prefix = $defaultDir;
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
     * 获取 Buzz 的 Browser 对象
     *
     * @return Browser
     */
    protected function getBrowser(): Browser
    {
        if (!$this->browser) {
            $client = new Curl(new ResponseFactory(), [
                'timeout' => 10,
                'verify' => false,
            ]);

            $this->browser = new Browser($client, new RequestFactory());
        }

        return $this->browser;
    }

    /**
     * 获取 Stream 工厂
     *
     * @return StreamFactoryInterface
     */
    protected function getStreamFactory(): StreamFactoryInterface
    {
        if (!$this->streamFactory) {
            $this->streamFactory = new StreamFactory();
        }

        return $this->streamFactory;
    }

    /**
     * 获取 Uri 工厂
     *
     * @return UriFactoryInterface
     */
    protected function getUriFactory(): UriFactoryInterface
    {
        if (!$this->uriFactory) {
            $this->uriFactory = new UriFactory();
        }

        return $this->uriFactory;
    }

    /**
     * 获取缩略图存储路径，用于 local, ftp, sftp
     *
     * @param  string $location
     * @param  string $size
     * @return string
     */
    protected function getThumbLocation(string $location, string $size): string
    {
        $locationArr = explode('.', $location);
        $locationArr[count($locationArr) - 2] .= "_{$size}";

        return implode('.', $locationArr);
    }

    /**
     * 裁剪图片，用于 local, ftp, sftp
     *
     * @param  StreamInterface $stream   文件流
     * @param  array           $thumbs   文件缩略图数组
     * @param  string          $location 原图保存路径（缩略图的路径根据原图路径计算）
     * @param  callable        $callback 回调函数
     */
    protected function crop(StreamInterface $stream, array $thumbs, string $location, callable $callback): void
    {
        ini_set('memory_limit', '300M');

        $image = ImageManagerStatic::make((string) $stream->getMetadata('uri'));
        $suffix = pathinfo($location, PATHINFO_EXTENSION);
        $originalWidth = $image->getWidth();
        $originalHeight = $image->getHeight();

        foreach ($thumbs as $size => [$width, $height]) {
            $tmpName = Str::guid() . '.' . $suffix;
            $newImage = clone $image;

            // 未指定高度时，等比例生成缩略图
            if (!$height) {
                $height = (int) ($originalHeight / $originalWidth * $width);
            }

            // 缩略图的宽高比和原图不一致，需要先裁剪
            if ($originalWidth / $originalHeight < $width / $height) {
                $cropHeight = (int) ($originalWidth / ($width / $height));
                $newImage->crop($originalWidth, $cropHeight);
            } elseif ($originalWidth / $originalHeight > $width / $height) {
                $cropWidth = (int) ($originalHeight * ($width / $height));
                $newImage->crop($cropWidth, $originalHeight);
            }

            // 如果原图比缩略图大，则缩小
            if ($width < $newImage->getWidth()) {
                $newImage->resize($width, $height);
            }

            $tmpPath = sys_get_temp_dir() . '/' . $tmpName;
            $newImage->save($tmpPath);
            $cropLocation = $this->getThumbLocation($location, $size);

            // 生成成功调用回调函数
            $callback($tmpPath, $cropLocation);
        }
    }
}
