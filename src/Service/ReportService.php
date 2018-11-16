<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;
use App\Traits\baseTraits;

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
    use baseTraits;

    /**
     * 获取举报列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->reportModel->paginate();

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
        $this->createValidator($reportableId, $reportableType, $reason);

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
     * @param int    $reportableId
     * @param string $reportableType
     * @param string $reason
     */
    private function createValidator(int $reportableId, string $reportableType, string $reason): void
    {
        $errors = [];

        // 验证 reportableType
        if (!$reportableType) {
            $errors['reportable_type'] = '举报类型不能为空';
        } elseif (!in_array($reportableType, ['question', 'answer', 'article', 'comment'])) {
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
    }

    /**
     * 删除一个举报
     *
     * @param int $reportId
     */
    public function delete(int $reportId): void
    {
        $this->reportModel->delete($reportId);
    }

    /**
     * 批量删除举报
     *
     * @param array $reportIds
     */
    public function batchDelete(array $reportIds): void
    {
        $this->reportModel->delete($reportIds);
    }

    public function handle($data): array
    {
        return $data;
    }

    /**
     * 为举报添加 relationship 字段
     * {
     *     user: {
     *         user_id: '',
     *         username: '',
     *         headline: '',
     *         avatar: {
     *             s: '',
     *             m: '',
     *             l: ''
     *         }
     *     },
     *     question: {},
     *     answer: {},
     *     article: {},
     *     comment: {}
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

        $userIds = array_unique(array_column($reports, 'user_id'));

        // id的数组 {question: [], answer: [], article: [], comment: []}
        $targetIds = [];

        // 对象的id为键名 {question: [], answer: [], article: [], comment: [], user: []}
        $targets = [];

        foreach ($reports as $report) {
            $targetIds[$report['reportable_type']][] = $report['reportable_id'];
        }

        // user
        $usersTmp = $this->userModel
            ->where(['user_id' => $userIds])
            ->field(['user_id', 'avatar', 'username', 'headline'])
            ->select();

        foreach ($usersTmp as $item) {
            $item = $this->userService->handle($item);
            $targets['user'][$item['user_id']] = [
                'user_id'  => $item['user_id'],
                'username' => $item['username'],
                'headline' => $item['headline'],
                'avatar'   => $item['avatar'],
            ];
        }

        // question、answer、article、comment
        $targetsTmp = [];
        foreach ($targetIds as $type => $ids) {
            $targetsTmp[$type] = $this->{$type . 'Service'}->getMultiple(array_unique($ids), false);
        }

        foreach ($targetsTmp as $type => $targetArrayTmp) {
            foreach ($targetArrayTmp as $target) {
                $targets[$type][$target[$type . '_id']] = $target;
            }
        }

        foreach ($reports as &$report) {
            $type = $report['reportable_type'];

            $report['relationship'] = [
                'user' => $targets['user'][$report['user_id']] ?? [],
                $type  => $targets[$type][$report['reportable_id']] ?? [],
            ];
        }

        if ($isArray) {
            return $reports;
        }

        return $reports[0];
    }
}
