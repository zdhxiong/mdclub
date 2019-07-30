<?php

declare(strict_types=1);

namespace MDClub\Service\User;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Helper\Validator;

/**
 * 用户更新
 */
class Update extends Abstracts
{
    /**
     * 验证用户信息
     *
     * @param array $data
     */
    protected function validation(array $data): void
    {
        $errors = [];

        if (isset($data['headline']) && !Validator::isMax($data['headline'], 40)) {
            $errors['headline'] = '一句话介绍不能超过 40 个字';
        }

        if (isset($data['bio']) && !Validator::isMax($data['bio'], 160)) {
            $errors['bio'] = '个人简介不能超过 160 个字';
        }

        if (isset($data['blog']) && !Validator::isMax($data['blog'], 255)) {
            $errors['blog'] = '个人主页不能超过 255 个字';
        }

        if (isset($data['company']) && !Validator::isMax($data['company'], 255)) {
            $errors['company'] = '公司名称不能超过 255 个字';
        }

        if (isset($data['location']) && !Validator::isMax($data['location'], 255)) {
            $errors['location'] = '地址不能超过 255 个字';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    /**
     * 更新指定用户的信息
     *
     * @param int   $userId
     * @param array $data
     */
    public function update(int $userId, array $data): void
    {
        $data = collect($data)->only(['headline', 'bio', 'blog', 'company', 'location'])->all();

        if (!$data) {
            return;
        }

        $this->validation($data);

        $updatedRow = $this->model->where('user_id', $userId)->update($data);

        if (!$updatedRow) {
            throw new ApiException(ApiError::USER_NOT_FOUND);
        }
    }
}
