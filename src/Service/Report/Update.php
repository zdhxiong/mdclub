<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;

/**
 * 更新举报
 */
class Update extends Abstracts
{
    /**
     * 创建举报
     *
     * @param  string $reportableType 举报目标类型
     * @param  int    $reportableId   举报目标ID
     * @param  string $reason         举报原因
     * @return int                    report_id
     */
    public function create(string $reportableType, int $reportableId, string $reason): int
    {
        $userId = $this->roleService->userIdOrFail();
        $this->createValidator($reportableType, $reportableId, $reason);

        return (int) $this->model->insert([
            'reportable_type' => $reportableType,
            'reportable_id'   => $reportableId,
            'user_id'         => $userId,
            'reason'          => $reason,
        ]);
    }

    /**
     * 创建举报前的验证
     *
     * @param string $reportableType
     * @param int    $reportableId
     * @param string $reason
     */
    private function createValidator(string $reportableType, int $reportableId, string $reason): void
    {
        $errors = [];

        // 验证 reportableType
        if (!$reportableType) {
            $errors['reportable_type'] = '举报类型不能为空';
        } elseif (!in_array($reportableType, ['question', 'answer', 'article', 'comment', 'user'])) {
            $errors['reportable_type'] = '举报类型错误';
        }

        // 验证 reportableId
        if (!$reportableId) {
            $errors['reportable_id'] = '举报对象ID不能为空';
        } elseif (!isset($errors['reportable_type'])) {
            if (!$this->{$reportableType . 'Model'}->has($reportableId)) {
                $errors['reportable_id'] = '举报对象不存在';
            }
        }

        // 验证 reason
        if (!$reason) {
            $errors['reason'] = '举报原因不能为空';
        } elseif (!ValidatorHelper::isMin($reason, 2)) {
            $errors['reason'] = '举报原因不能少于 2 个字符';
        } elseif (!ValidatorHelper::isMax($reason, 200)) {
            $errors['reason'] = '举报原因不能超过 200 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        // 验证是否已举报过
        $isExist = $this->model
            ->where([
                'reportable_type' => $reportableType,
                'reportable_id' => $reportableId,
                'user_id' => $this->roleService->userId(),
            ])
            ->has();

        if ($isExist) {
            throw new ApiException(ErrorConstant::REPORT_ALREADY_SUBMITTED);
        }
    }
}
