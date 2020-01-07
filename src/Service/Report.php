<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\ReportModel;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Validator\ReportValidator;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Traits\Getable;

/**
 * 举报服务。可举报：question, article, answer, comment, user
 */
class Report extends Abstracts implements GetableInterface
{
    use Getable;

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Report::class;
    }

    /**
     * 检查举报目标是否存在
     *
     * @param string $reportableType
     * @param int    $reportableId
     *
     * @throws ApiException
     */
    public function hasTargetOrFail(string $reportableType, int $reportableId): void
    {
        if (!in_array($reportableType, ['question', 'answer', 'article', 'comment', 'user'])) {
            throw new ApiException(ApiErrorConstant::REPORT_TARGET_NOT_FOUND);
        }

        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . lcfirst($reportableType) . 'Service';
        $class::hasOrFail($reportableId);
    }

    /**
     * 获取举报理由
     *
     * @param string $reportableType
     * @param int    $reportableId
     *
     * @return array
     */
    public function getReasons(string $reportableType, int $reportableId): array
    {
        return ReportModel
            ::where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->order('create_time', 'DESC')
            ->paginate();
    }

    /**
     * 删除举报
     *
     * @param string $reportableType
     * @param int    $reportableId
     */
    public function delete(string $reportableType, int $reportableId): void
    {
        ReportModel
            ::where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->delete();
    }

    /**
     * 批量删除举报组
     *
     * @param array $targets
     */
    public function deleteMultiple(array $targets): void
    {
        $where = [];

        foreach ($targets as $key => $target) {
            if (strpos($target, ':') > 0) {
                [$reportableType, $reportableId] = explode(':', $target);

                $where['OR']['AND #' . $key] = [
                    'reportable_id' => $reportableId,
                    'reportable_type' => $reportableType,
                ];
            }
        }

        if ($where) {
            ReportModel::where($where)->delete();
        }
    }

    /**
     * 创建举报
     *
     * @param string $reportableType 举报目标类型
     * @param int    $reportableId   举报目标ID
     * @param array  $data           [reason]
     *
     * @return int
     */
    public function create(string $reportableType, int $reportableId, array $data): int
    {
        $data = ReportValidator::create($reportableType, $reportableId, $data);

        return (int)ReportModel
            ::set('reportable_type', $reportableType)
            ->set('reportable_id', $reportableId)
            ->set('user_id', Auth::userId())
            ->set('reason', $data['reason'])
            ->insert();
    }
}
