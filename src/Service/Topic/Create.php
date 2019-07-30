<?php

declare(strict_types=1);

namespace MDClub\Service\Topic;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Validator;
use MDClub\Traits\Brandable;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 创建话题
 */
class Create extends Abstracts
{
    use Brandable;

    /**
     * 创建话题前的字段验证
     *
     * @param string                $name
     * @param string                $description
     * @param UploadedFileInterface $cover
     */
    protected function createValidation(string $name, string $description, UploadedFileInterface $cover = null): void
    {
        $errors = [];

        // 验证名称
        if (!$name) {
            $errors['name'] = '名称不能为空';
        } elseif (!Validator::isMax($name, 20)) {
            $errors['name'] = '名称长度不能超过 20 个字符';
        }

        // 验证描述
        if (!$description) {
            $errors['description'] = '描述不能为空';
        } elseif (!Validator::isMax($description, 1000)) {
            $errors['description'] = '描述不能超过 1000 个字符';
        }

        // 验证图片
        if (!$cover) {
            $errors['cover'] = '请选择要上传的封面图片';
        } elseif ($coverError = $this->validateImage($cover)) {
            $errors['cover'] = $coverError;
        }

        if (!$errors && $this->model->where('name', $name)->has()) {
            $errors['name'] = '该名称已存在';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    /**
     * 创建话题
     *
     * @param  string                $name
     * @param  string                $description
     * @param  UploadedFileInterface $cover
     * @return int
     */
    public function create(string $name, string $description, UploadedFileInterface $cover = null): int
    {
        $this->createValidation($name, $description, $cover);

        // 添加话题
        $topicId = (int)$this->model->insert([
            'name' => $name,
            'description' => $description,
        ]);

        // 保存图片
        $fileName = $this->uploadImage($topicId, $cover);

        // 更新图片到话题信息中
        $this->model
            ->where('topic_id', $topicId)
            ->update('cover', $fileName);

        return $topicId;
    }
}
