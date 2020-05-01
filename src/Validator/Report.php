<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\ReportModel;
use MDClub\Facade\Service\ReportService;

/**
 * 举报验证
 */
class Report extends Abstracts
{
    protected $attributes = [
        'reportable_type' => '举报类型',
        'reportable_id' => '举报对象',
        'reason' => '举报原因',
    ];

    /**
     * 创建时验证
     *
     * @param string $reportableType
     * @param int    $reportableId
     * @param array  $data [reason]
     *
     * @return array
     */
    public function create(string $reportableType, int $reportableId, array $data): array
    {
        ReportService::hasTargetOrFail($reportableType, $reportableId);

        $data = $this->data($data)
            ->field('reason')->exist()->length(2, 200)
            ->validate();

        $reported = ReportModel
            ::where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->where('user_id', Auth::userId())
            ->has();

        if ($reported) {
            throw new ApiException(ApiErrorConstant::REPORT_ALREADY_SUBMITTED);
        }

        return $data;
    }
}
