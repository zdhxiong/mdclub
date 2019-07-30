<?php

declare(strict_types=1);

namespace MDClub\Service\Image;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Validator;

/**
 * 更新图片信息
 */
class Update extends Abstracts
{
    /**
     * 更新图片信息
     *
     * @param  string $key  文件key
     * @param  array  $data 更新的数据（仅允许更新 filename, item_type, item_id）
     */
    public function update(string $key, array $data): void
    {
        $data = collect($data)->only(['filename', 'item_type', 'item_id'])->all();

        if (!$data) {
            return;
        }

        $this->validation($data);

        $this->model->where('key', $key)->update($data);
    }

    /**
     * 更新前验证
     *
     * @param  array $data
     * @throws ValidationException
     */
    protected function validation(array $data): void
    {
        $errors = [];

        if (isset($data['filename']) && !Validator::isMax($data['filename'], 255)) {
            $errors['filename'] = '文件名不能超过 255 个字符';
        }

        if (isset($data['item_type']) && !in_array($data['item_type'], ['question', 'article', 'answer'])) {
            $errors['item_type'] = 'item_type 只能为 question,article,answer 之一';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if (isset($data['item_id']) && !$this->{$data['item_type'] . 'Service'}->has($data['item_id'])) {
            $errors['item_id'] = '关联ID 不存在';

            throw new ValidationException($errors);
        }
    }
}
