<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\UploadErrorConstant;
use App\Helper\RequestHelper;
use App\Helper\StringHelper;
use PHPImageWorkshop\ImageWorkshop;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 对象的标识图 （user-avatar、user-cover、topic-cover）
 *
 * Trait BrandableTraits
 * @package App\Traits
 */
trait BrandableTraits
{
    /**
     * 图片类型，包括 user-avatar、user-cover、topic-cover
     *
     * @return string
     */
    abstract protected function getBrandType(): string;

    /**
     * 生成的图片尺寸数组
     *
     * @return array
     */
    abstract protected function getBrandWidths(): array;

    /**
     * 图片的高度/宽度的比例，为 0 则不裁剪图片
     *
     * @return float
     */
    abstract protected function getBrandScale(): float;

    /**
     * 获取默认图片地址
     *
     * @return array
     */
    abstract protected function getDefaultBrandUrls(): array;

    /**
     * 获取文件存储的相对路径
     *
     * @param  int    $id
     * @return string
     */
    protected function getBrandPath(int $id): string
    {
        $hash = md5((string)$id);

        return implode('/', [
            $this->getBrandType(),
            substr($hash, 0, 2),
            substr($hash, 2, 2),
        ]);
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
    protected function getBrandFilename(string $filename, string $size = ''): string
    {
        if (!in_array($size, array_keys($this->getBrandWidths()))) {
            $size = '';
        }

        if (!$size) {
            return $filename;
        }

        $storageType = $this->optionService->get('storage_type');
        $isSupportWebp = RequestHelper::isSupportWebp($this->request);

        $width = $this->getBrandWidths()[$size];
        $height = round($width * $this->getBrandScale());

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
    protected function getFullBrandFilename(int $id, string $filename, string $size = ''): string
    {
        return $this->getBrandPath($id) . '/' . $this->getBrandFilename($filename, $size);
    }

    /**
     * 获取文件的访问路径
     *
     * @param  int    $id
     * @param  string $filename
     * @param  string $size     默认为原图
     * @return string
     */
    public function getBrandUrl(int $id, string $filename = null, string $size = ''): string
    {
        if (!$filename) {
            return '';
        }

        return $this->getStorageUrl() . $this->getFullBrandFilename($id, $filename, $size);
    }

    /**
     * 获取各种尺寸文件的访问路径数组，除了原图
     *
     * @param  int    $id
     * @param  string $filename
     * @return array
     */
    public function getBrandUrls(int $id, string $filename = null): array
    {
        $array = [];
        $default = $this->getDefaultBrandUrls();

        foreach (array_keys($this->getBrandWidths()) as $size) {
            $array[$size] = $this->getBrandUrl($id, $filename, $size) ?: ($default[$size] ?? '');
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
        $fullFilename= $this->getFullBrandFilename($id, $filename);

        // 删除原图
        try {
            $this->storage->delete($fullFilename);
        } catch (\Exception $e) {}

        // 仅 local 和 ftp 需要删除裁剪后的图片
        if (in_array($this->optionService->get('storage_type'), ['local', 'ftp'])) {
            foreach (array_keys($this->getBrandWidths()) as $size) {
                $fullFilename = $this->getFullBrandFilename($id, $filename, $size);
                try {
                    $this->storage->delete($fullFilename);
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
        $fullFilename = $this->getFullBrandFilename($id, $filename);

        // 写入原始文件
        $this->storage->write($fullFilename, $file->getStream()->getContents());

        // 仅 local 和 ftp 需要预先裁剪图片，云存储不需要裁剪
        if (in_array($this->optionService->get('storage_type'), ['local', 'ftp'])) {
            ini_set('memory_limit', '300M');

            $image = ImageWorkshop::initFromPath($file->getStream()->getMetadata('uri'));
            $imageWidth = $image->getWidth();
            $imageHeight = $image->getHeight();

            // 裁剪图片
            if ($this->getBrandScale() === 1) {
                // 按最小边裁剪成正方形
                if ($imageWidth !== $imageHeight) {
                    $image->cropMaximumInPercent(0, 0, 'MM');
                }
            } else {
                // 裁剪成长方形
                if ($imageHeight / $imageWidth > $this->getBrandScale()) {
                    $height = round($imageWidth * $this->getBrandScale());
                    $image->cropInPixel($imageWidth, $height, 0, 0, 'MM');
                } elseif ($imageHeight / $imageWidth < $this->getBrandScale()) {
                    $width = round($imageHeight / $this->getBrandScale());
                    $image->cropInPixel($width, $imageHeight, 0, 0, 'MM');
                }
            }

            // 生成缩略图
            foreach ($this->getBrandWidths() as $size => $width) {
                $newImage = clone $image;

                if ($width < $newImage->getWidth()) {
                    $newImage->resizeInPixel($width, null, true);
                }

                $newImage->save(sys_get_temp_dir(), $filename);
                $this->storage->write(
                    $this->getFullBrandFilename($id, $filename, $size),
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
