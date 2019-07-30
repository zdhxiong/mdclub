<?php

declare(strict_types=1);

namespace MDClub\Service\Topic;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Helper\Validator;
use MDClub\Traits\Brandable;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 更新话题
 */
class Update extends Abstracts
{
    use Brandable;

    /**
     * 更新话题前的字段验证
     *
     * @param  int                   $topicId
     * @param  string                $name
     * @param  string                $description
     * @param  UploadedFileInterface $cover
     * @return array                 $topicInfo
     */
    private function updateValidation(
        int $topicId,
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): array {
        $topicInfo = $this->model->get($topicId);

        if (!$topicInfo) {
            throw new ApiException(ApiError::TOPIC_NOT_FOUND);
        }

        $errors = [];

        // 验证名称
        if (!is_null($name)) {
            if (!$name) {
                $errors['name'] = '名称不能为空';
            } elseif (!Validator::isMax($name, 20)) {
                $errors['name'] = '名称长度不能超过 20 个字符';
            }
        }

        // 验证描述
        if (!is_null($description)) {
            if (!$description) {
                $errors['description'] = '描述不能为空';
            } elseif (!Validator::isMax($description, 1000)) {
                $errors['description'] = '描述不能超过 1000 个字符';
            }
        }

        // 验证图片
        if (!is_null($cover)) {
            if ($coverError = $this->validateImage($cover)) {
                $errors['cover'] = $coverError;
            }
        }

        if (
            !$errors
            && !is_null($name)
            && $this->model->where(['name' => $name, 'topic_id[!]' => $topicId])->has()
        ) {
            $errors['name'] = '该名称已存在';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $topicInfo;
    }

    /**
     * 更新话题
     *
     * @param  int                   $topicId
     * @param  string                $name
     * @param  string                $description
     * @param  UploadedFileInterface $cover
     */
    public function update(
        int $topicId,
        string $name = null,
        string $description = null,
        UploadedFileInterface $cover = null
    ): void {
        $topicInfo = $this->updateValidation($topicId, $name, $description, $cover);

        $data = [];
        !is_null($name) && $data['name'] = $name;
        !is_null($description) && $data['description'] = $description;

        if ($data) {
            $this->model
                ->where('topic_id', $topicId)
                ->update($data);
        }

        if (!is_null($cover)) {
            // 先删除旧图片
            $this->deleteImage($topicId, $topicInfo['cover']);

            // 上传并更新图片
            $fileName = $this->uploadImage($topicId, $cover);
            $this->model
                ->where('topic_id', $topicId)
                ->update('cover', $fileName);
        }
    }
}
