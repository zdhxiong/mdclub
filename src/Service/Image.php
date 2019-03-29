<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Constant\UploadErrorConstant;
use App\Exception\ApiException;
use App\Helper\ArrayHelper;
use App\Helper\RequestHelper;
use App\Helper\StringHelper;
use PHPImageWorkshop\ImageWorkshop;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Stream;

/**
 * 图片
 *
 * 如果使用本地存储，则每张图片需要保存为：
 * 1. 原图
 * 2. 宽度为 650px，高度自适应（存储为本地图片时，以 _r 为后缀）
 * 3. 宽度为 132px，高度为 88px，居中裁剪的缩略图（存储为本地图片时，以 _t 为后缀）
 *
 * @property-read \App\Model\Image      currentModel
 *
 * Class ImageService
 * @package App\Service
 */
class Image extends ServiceAbstracts
{
    /**
     * 图片尺寸
     *
     * @return array
     */
    protected function getSize(): array
    {
        return [
            't' => [132, 88],
            'r' => [650, 0],
        ];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['hash', 'item_type', 'item_id', 'user_id'];
    }

    /**
     * 获取图片列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->container->imageModel
            ->where($this->getWhere())
            ->order(['create_time' => 'DESC'])
            ->paginate();

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 根据图片 hash 获取图片信息
     *
     * @param  string $hash
     * @param  bool   $withRelationship
     * @return array
     */
    public function getInfo(string $hash, bool $withRelationship = false): array
    {
        $info = $this->container->imageModel->get($hash);

        if (!$info) {
            throw new ApiException(ErrorConstant::IMAGE_NOT_FOUND);
        }

        $info = $this->handle($info);

        if ($withRelationship) {
            $info = $this->addRelationship($info);
        }

        return $info;
    }

    /**
     * 更新图片信息
     *
     * @param  string $hash
     * @param  array  $data 仅允许更新 item_type 和 item_id 字段
     * @return bool
     */
    public function updateInfo(string $hash, array $data): bool
    {
        $canUpdateFields = ['filename', 'item_type', 'item_id'];
        $data = ArrayHelper::filter($data, $canUpdateFields);

        if (!$data) {
            return true;
        }

        return !!$this->container->imageModel->where(['hash' => $hash])->update($data);
    }

    /**
     * 获取图片存储的相对路径
     *
     * @param  string $hash
     * @param  int    $timestamp
     * @return string
     */
    protected function getPath(string $hash, int $timestamp): string
    {
        $path = implode('/', ['image', date('Y-m/d', $timestamp), substr($hash, 0, 2)]);

        return "{$path}/{$hash}";
    }

    /**
     * 获取各种尺寸的图片访问路径
     *
     * @param  string $hash
     * @param  int    $timestamp
     * @return array
     */
    public function getUrls(string $hash, int $timestamp): array
    {
        $path = $this->getPath($hash, $timestamp);
        $thumbs = $this->getSize();

        return $this->container->storage->get($path, $thumbs);
    }

    /**
     * 获取图片后缀名
     *
     * @param  UploadedFileInterface $file
     * @return string
     */
    protected function getSuffix(UploadedFileInterface $file): string
    {
        switch ($file->getClientMediaType()) {
            case 'image/gif':
                $suffix = 'gif';
                break;
            case 'image/png':
                $suffix = 'png';
                break;
            default:
                $suffix = 'jpg';
                break;
        }

        return $suffix;
    }

    /**
     * 上传图片
     *
     * @param  int                   $userId 用户ID
     * @param  UploadedFileInterface $file   UploadedFile对象
     * @return string                        文件名（不含路径）
     */
    public function upload(int $userId, UploadedFileInterface $file = null): string
    {
        $uploadErr = '';

        if (is_null($file)) {
            $uploadErr = '请选择要上传的图片';
        }

        if (!$uploadErr) {
            $uploadErr = $this->validateImage($file);
        }

        if ($uploadErr) {
            throw new ApiException(ErrorConstant::SYSTEM_IMAGE_UPLOAD_FAILED, false, $uploadErr);
        }

        $suffix = $this->getSuffix($file);
        $hash = StringHelper::guid() . '.' . $suffix;
        $timestamp = (int)$this->container->request->getServerParam('REQUEST_TIME');
        $path = $this->getPath($hash, $timestamp);

        $this->container->storage->write($path, $file->getStream(), $this->getSize());

        list($imageWidth, $imageHeight) = getimagesize($file->getStream()->getMetadata('uri'));

        $this->container->imageModel->insert([
            'hash'     => $hash,
            'filename' => $file->getClientFilename(),
            'width'    => $imageWidth,
            'height'   => $imageHeight,
            'user_id'  => $userId,
        ]);

        return $hash;
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
        } elseif (!in_array($file->getClientMediaType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            return '仅允许上传 jpg、png 或 gif 图片';
        }

        return false;
    }

    /**
     * 删除图片
     *
     * @param string $hash
     */
    public function delete(string $hash): void
    {
        $image = $this->container->imageModel->field(['hash', 'create_time'])->get($hash);

        if ($image) {
            $this->container->imageModel->delete($hash);

            $path = $this->getPath($hash, $image['create_time']);
            $thumbs = $this->getSize();

            $this->container->storage->delete($path, $thumbs);
        }
    }

    /**
     * 批量删除图片
     *
     * @param array $hashs
     */
    public function deleteMultiple(array $hashs): void
    {
        if (!$hashs) {
            return;
        }

        $images = $this->container->imageModel->field(['hash', 'create_time'])->select($hashs);

        if (!$images) {
            return;
        }

        $this->container->imageModel->delete(array_column($images, 'hash'));

        foreach ($images as $image) {
            $path = $this->getPath($image['hash'], $image['create_time']);
            $thumbs = $this->getSize();

            $this->container->storage->delete($path, $thumbs);
        }
    }

    /**
     * 处理图片信息
     *
     * @param array $images 图片信息，或多个图片组成的数组
     * @return array
     */
    public function handle(array $images): array
    {
        if (!$images) {
            return $images;
        }

        if (!$isArray = is_array(current($images))) {
            $images = [$images];
        }

        foreach ($images as &$image) {
            $image['urls'] = $this->getUrls($image['hash'], $image['create_time']);
        }

        if ($isArray) {
            return $images;
        }

        return $images[0];
    }

    /**
     * 为图片信息添加 relationship 字段
     * {
     *     user: {},
     *     question: {},
     *     answer: {},
     *     article: {}
     * }
     *
     * @param array $images
     * @return array
     */
    public function addRelationship(array $images): array
    {
        if (!$images) {
            return $images;
        }

        if (!$isArray = is_array(current($images))) {
            $images = [$images];
        }

        $targetIds = []; // 键名为对象类型，键值为对象的ID数组
        $targets = []; // 键名为对象类型，键值为对象ID和对象信息的多维数组
        $targetTypes = ['user', 'question', 'answer', 'article'];

        foreach ($images as $image) {
            $targetIds['user'][] = $image['user_id'];

            if ($image['item_type']) {
                $targetIds[$image['item_type']][] = $image['item_id'];
            }
        }

        foreach ($targetTypes as $type) {
            if (isset($targetIds[$type])) {
                $targetIds[$type] = array_unique($targetIds[$type]);
                $targets[$type] = $this->container->{$type . 'Service'}->getInRelationship($targetIds[$type]);
            }
        }

        foreach ($images as &$image) {
            $image['relationship']['user'] = $targets['user'][$image['user_id']];
            if ($image['item_type']) {
                $image['relationship'][$image['item_type']] = $targets[$image['item_type']][$image['item_id']];
            }
        }

        if ($isArray) {
            return $images;
        }

        return $images[0];
    }
}
