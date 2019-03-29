<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Abstracts\ContainerAbstracts;
use App\Helper\StringHelper;
use PHPImageWorkshop\ImageWorkshop;
use Psr\Http\Message\StreamInterface;

/**
 * Class AbstractAdapter
 * @package App\Library\StorageAdapter
 */
abstract class AbstractAdapter extends ContainerAbstracts
{
    /**
     * 获取缩略图存储路径，用于 local、ftp、sftp
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
     * 裁剪图片
     *
     * @param  StreamInterface $stream   文件流
     * @param  array           $thumbs   文件缩略图数组
     * @param  string          $location 原图保存路径（缩略图的路径根据原图路径计算）
     * @param  callable        $callback 回调函数
     */
    protected function crop(StreamInterface $stream, array $thumbs, string $location, callable $callback): void
    {
        ini_set('memory_limit', '300M');

        $image = ImageWorkshop::initFromPath($stream->getMetadata('uri'));
        $originalWidth = $image->getWidth();
        $originalHeight = $image->getHeight();

        foreach ($thumbs as $size => [$width, $height]) {
            $tmpName = StringHelper::guid() . '.png';
            $newImage = clone $image;

            // 未指定高度时，等比例生成缩略图
            if (!$height) {
                $height = $originalHeight / $originalWidth * $width;
            }

            // 缩略图的宽高比和原图不一致，需要先裁剪
            if ($width / $height !== $originalWidth / $originalHeight) {
                if ($width === $height) {
                    // 裁剪成正方形
                    $newImage->cropMaximumInPercent(0, 0, 'MM');
                } else {
                    // 裁剪成长方形
                    if ($originalWidth / $originalHeight < $width / $height) {
                        $cropHeight = round($originalWidth / ($width / $height));
                        $newImage->cropInPixel($originalWidth, $cropHeight, 0, 0, 'MM');
                    } else {
                        $cropWidth = round($originalHeight * ($width / $height));
                        $newImage->cropInPixel($cropWidth, $originalHeight, 0, 0, 'MM');
                    }
                }
            }

            // 生成缩略图
            if ($width < $newImage->getWidth()) {
                $newImage->resizeInPixel($width, null, true);
            }

            $newImage->save(sys_get_temp_dir(), $tmpName);

            // 生成成功后的回调
            $pathTmp = sys_get_temp_dir() . '/' . $tmpName;
            $cropLocation = $this->getThumbLocation($location, $size);

            $callback($pathTmp, $cropLocation);
        }
    }
}
