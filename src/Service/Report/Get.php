<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Traits\Getable;
use Medoo\Medoo;
use Tightenco\Collect\Support\Collection;

/**
 * 获取举报
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['reportable_type'];
    }

    /**
     * 对结果中的内容进行处理
     *
     * @param  array $reports
     * @return array
     */
    public function addFormatted(array $reports): array
    {
        return $reports;
    }

    /**
     * 为结果添加相关信息
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
        $targetIds = []; // 键名为对象类型，键值为对象的ID数组
        $targets = []; // 键名为对象类型，键值为对象ID和对象信息的多维数组
        $targetTypes = ['question', 'answer', 'article', 'comment', 'user'];

        foreach ($reports as $report) {
            $targetIds[$report['reportable_type']][] = $report['reportable_id'];

            if (isset($report['user_id'])) {
                $targetIds['user'][] = $report['user_id'];
            }
        }

        foreach ($targetTypes as $type) {
            if (isset($targetIds[$type])) {
                $targetIds[$type] = array_unique($targetIds[$type]);
                $targets[$type] = $this->{$type . 'Service'}->getInRelationship($targetIds[$type]);
            }
        }

        foreach ($reports as &$report) {
            if (isset($report['user_id'])) {
                $report['relationship']['reporter'] = $targets['user'][$report['user_id']];
            }

            $report['relationship'][$report['reportable_type']] = $targets[$report['reportable_type']][$report['reportable_id']];
        }

        unset($report);

        return $reports;
    }

    /**
     * 获取被举报的内容列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $result = $this->model
            ->where($this->getWhere())
            ->field([
                'reporter_count' => Medoo::raw('COUNT(<report_id>)'),
                'reportable_id',
                'reportable_type',
            ])
            ->order('reporter_count', 'DESC')
            ->group([
                'reportable_id',
                'reportable_type',
            ])
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取被举报内容的举报详情
     *
     * @param  string $reportableType
     * @param  int    $reportableId
     * @return array
     */
    public function getDetailList(string $reportableType, int $reportableId): array
    {
        $result = $this->model
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->order('create_time', 'DESC')
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }
}
