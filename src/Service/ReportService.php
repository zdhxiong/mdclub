<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;
use App\Traits\BaseTraits;

/**
 * 举报
 *
 * @property-read \App\Model\ReportModel      currentModel
 *
 * Class ReportService
 * @package App\Service
 */
class ReportService extends ServiceAbstracts
{
    use BaseTraits;

    /**
     * 获取举报列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->reportModel
            ->where($this->getWhere())
            ->order($this->getOrder())
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 创建举报
     *
     * @param  int    $userId         举报者ID
     * @param  int    $reportableId   举报目标ID
     * @param  string $reportableType 举报目标类型
     * @param  string $reason         举报原因
     * @return int                    report_id
     */
    public function create(int $userId, int $reportableId, string $reportableType, string $reason): int
    {
        $this->createValidator($userId, $reportableId, $reportableType, $reason);

        return (int)$this->reportModel->insert([
            'reportable_id'   => $reportableId,
            'reportable_type' => $reportableType,
            'user_id'         => $userId,
            'reason'          => $reason,
        ]);
    }

    /**
     * 创建举报前的验证
     *
     * @param int    $userId
     * @param int    $reportableId
     * @param string $reportableType
     * @param string $reason
     */
    private function createValidator(int $userId, int $reportableId, string $reportableType, string $reason): void
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
        $isExist = $this->reportModel
            ->where([
                'reportable_id' => $reportableId,
                'reportable_type' => $reportableType,
                'user_id' => $userId,
            ])
            ->has();

        if ($isExist) {
            throw new ApiException(ErrorConstant::REPORT_ALREADY_SUBMITTED);
        }
    }

    public function handle($data): array
    {
        return $data;
    }

    /**
     * 为举报添加 relationship 字段
     * {
     *     reporter: {},
     *     question: {},
     *     answer: {},
     *     article: {},
     *     comment: {},
     *     user: {}
     * }
     *
     * @param  array $reports
     * @return array
     */
    public function addRelationship(array $reports): array
    {
        if (!$reports) {
            return $reports;
        }

        if (!$isArray = is_array(current($reports))) {
            $reports = [$reports];
        }

        $targetIds = []; // 键名为对象类型，键值为对象的ID数组
        $targets = []; // 键名为对象类型，键值为对象ID和对象信息的多维数组
        $targetTypes = ['question', 'answer', 'article', 'comment', 'user'];

        foreach ($reports as $report) {
            $targetIds[$report['reportable_type']][] = $report['reportable_id'];
            $targetIds['user'][] = $report['user_id'];
        }

        foreach ($targetTypes as $type) {
            if (isset($targetIds[$type])) {
                $targetIds[$type] = array_unique($targetIds[$type]);
                $targets[$type] = $this->{$type . 'Service'}->getInRelationship($targetIds[$type]);
            }
        }

        foreach ($reports as &$report) {
            $report['relationship'] = [
                'reporter' => $targets['user'][$report['user_id']],
                $report['reportable_type'] => $targets[$report['reportable_type']][$report['reportable_id']],
            ];
        }

        if ($isArray) {
            return $reports;
        }

        return $reports[0];
    }
}
