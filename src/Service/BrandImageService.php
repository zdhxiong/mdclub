<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Http\Message\UploadedFileInterface;
use App\Constant\UploadErrorConstant;
use App\Helper\StringHelper;
use PHPImageWorkshop\ImageWorkshop;

/**
 * 头像、封面图片抽象类
 *
 * Class BrandImageService
 * @package App\Service
 */
abstract class BrandImageService extends Service
{
    /**
     * @var string 图片类型，包括 user-avatar、user-cover、topic-cover
     */
    protected $imageType;

    /**
     * @var array 生成的图片尺寸数组
     */
    protected $imageWidths;

    /**
     * @var float 图片的高度/宽度的比例，为 0 则不裁剪图片
     */
    protected $imageScale;

    /**
     * 获取默认图片地址
     *
     * @return array
     */
    abstract protected function getDefaultImageUrls(): array;

    /**
     * 获取文件存储的相对路径
     *
     * @param  int    $id
     * @return string
     */
    protected function getImagePath(int $id): string
    {
        $hash = md5((string)$id);

        return implode('/', [
            $this->imageType,
            substr($hash, 0, 2),
            substr($hash, 2, 2),
        ]);
    }

    /**
     * 判断是否支持 webp
     *
     * @return bool
     */
    protected function isSupportWebp(): bool
    {
        return strpos($this->request->getServerParam('HTTP_ACCEPT'), 'image/webp');
    }

    /**
     * 获取文件名（带后缀、或图片裁剪参数）
     *
     * 如果是 local 或 ftp，则在上传时已生成不同尺寸的图片
     * 如果是 aliyun_oss、upyun、qiniu 存储，则添加 url 参数
     *
     * @param  string $filename  带图片后缀，用 . 分隔
     * @param  string $size      默认为原图
     * @return string
     */
    protected function getImageFilename(string $filename, string $size = ''): string
    {
        if (!in_array($size, array_keys($this->imageWidths))) {
            $size = '';
        }

        if (!$size) {
            return $filename;
        }

        $storageType = $this->optionService->get('storage_type');
        $isSupportWebp = $this->isSupportWebp();

        $width = $this->imageWidths[$size];
        $height = round($width * $this->imageScale);

        switch ($storageType) {
            // local 和 ftp，返回已裁剪好的图片
            case 'local':
            case 'ftp':
                list($name, $suffix) = explode('.', $filename);
                return "{$name}_{$size}.{$suffix}";

            // aliyun_oss 添加缩略图参数：https://help.aliyun.com/document_detail/44688.html
            case 'aliyun_oss':
                $params = "?x-oss-process=image/resize,m_fill,w_{$width},h_{$height},limit_0";
                $params .= $isSupportWebp ? '/format,webp' : '';

                return $filename . $params;

            // upyun 添加缩略图参数：https://help.upyun.com/knowledge-base/image/#e7bca9e5b08fe694bee5a4a7
            case 'upyun':
                $params = "!/both/{$width}x{$height}";
                $params .= $isSupportWebp ? '/format/webp' : '';

                return $filename . $params;

            // qiniu 添加缩略图参数：https://developer.qiniu.com/dora/manual/1279/basic-processing-images-imageview2
            case 'qiniu':
                $params = "?imageView2/1/w/{$width}/h/{$height}";
                $params .= $isSupportWebp ? '/format/webp' : '';

                return $filename . $params;
        }

        throw new \Exception('不存在指定的存储类型：' . $storageType);
    }

    /**
     * 获取包含相对路径的文件名
     *
     * @param  int    $id
     * @param  string $filename
     * @param  string $size     默认为原图
     * @return string
     */
    protected function getFullImageFilename(int $id, string $filename, string $size = ''): string
    {
        return $this->getImagePath($id) . '/' . $this->getImageFilename($filename, $size);
    }

    /**
     * 获取文件的访问路径
     *
     * @param  int    $id
     * @param  string $filename
     * @param  string $size     默认为原图
     * @return string
     */
    public function getImageUrl(int $id, string $filename = null, string $size = ''): string
    {
        if (!$filename) {
            return '';
        }

        return $this->getStorageUrl() . $this->getFullImageFilename($id, $filename, $size);
    }

    /**
     * 获取各种尺寸文件的访问路径数组，除了原图
     *
     * @param  int    $id
     * @param  string $filename
     * @return array
     */
    public function getImageUrls(int $id, string $filename = null): array
    {
        $array = [];
        $default = $this->getDefaultImageUrls();

        foreach (array_keys($this->imageWidths) as $size) {
            $array[$size] = $this->getImageUrl($id, $filename, $size) ?: ($default[$size] ?? '');
        }

        return $array;
    }

    /**
     * 删除图片
     *
     * @param  int    $id
     * @param  string $filename
     */
    public function deleteImage(int $id, string $filename): void
    {
        $fullFilename= $this->getFullImageFilename($id, $filename);

        // 删除原图
        try {
            $this->filesystem->delete($fullFilename);
        } catch (\Exception $e) {}

        // 仅 local 和 ftp 需要删除裁剪后的图片
        if (in_array($this->optionService->get('storage_type'), ['local', 'ftp'])) {
            foreach (array_keys($this->imageWidths) as $size) {
                $fullFilename = $this->getFullImageFilename($id, $filename, $size);
                try {
                    $this->filesystem->delete($fullFilename);
                } catch (\Exception $e) {}
            }
        }
    }

    /**
     * 上传图片，上传前需要调用 validateImage 验证图片
     *
     * @param  int                   $id
     * @param  UploadedFileInterface $file UploadedFile对象
     * @return string                      文件名（不含路径）
     */
    public function uploadImage(int $id, UploadedFileInterface $file): string
    {
        $token = StringHelper::guid();
        $suffix = $file->getClientMediaType() === 'image/png' ? 'png' : 'jpg';
        $filename = "{$token}.{$suffix}";
        $fullFilename = $this->getFullImageFilename($id, $filename);

        // 写入原始文件
        $this->filesystem->write($fullFilename, $file->getStream()->getContents());

        // 仅 local 和 ftp 需要预先裁剪图片，云存储不需要裁剪
        if (in_array($this->optionService->get('storage_type'), ['local', 'ftp'])) {
            ini_set('memory_limit', '300M');

            $image = ImageWorkshop::initFromPath($file->getStream()->getMetadata('uri'));
            $imageWidth = $image->getWidth();
            $imageHeight = $image->getHeight();

            // 裁剪图片
            if ($this->imageScale === 1) {
                // 按最小边裁剪成正方形
                if ($imageWidth !== $imageHeight) {
                    $image->cropMaximumInPercent(0, 0, 'MM');
                }
            } else {
                // 裁剪成长方形
                if ($imageHeight / $imageWidth > $this->imageScale) {
                    $height = round($imageWidth * $this->imageScale);
                    $positionY = ($imageHeight - $height) / 2;
                    $image->cropInPixel($imageWidth, $height, 0, $positionY);
                } elseif ($imageHeight / $imageWidth < $this->imageScale) {
                    $width = round($imageHeight / $this->imageScale);
                    $positionX = ($imageWidth - $width) / 2;
                    $image->cropInPixel($width, $imageHeight, $positionX, 0);
                }
            }

            // 生成缩略图
            foreach ($this->imageWidths as $size => $width) {
                $newImage = clone $image;

                if ($width < $newImage->getWidth()) {
                    $newImage->resizeInPixel($width, null, true);
                }

                $newImage->save(sys_get_temp_dir(), $filename);

                $this->filesystem->write(
                    $this->getFullImageFilename($id, $filename, $size),
                    file_get_contents(sys_get_temp_dir() . '/' . $filename)
                );
            }
        }

        return $filename;
    }

    /**
     * 对上传的文件进行验证
     *
     * @param  UploadedFileInterface $file
     * @return bool|string                 错误描述，false表示没有错误
     */
    protected function validateImage(UploadedFileInterface $file)
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return UploadErrorConstant::getMessage()[$file->getError()];
        } elseif (!in_array($file->getClientMediaType(), ['image/jpeg', 'image/png'])) {
            return '仅允许上传 jpg 或 png 图片';
        }

        return false;
    }
}
