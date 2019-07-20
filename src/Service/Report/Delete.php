<?php

declare(strict_types=1);

namespace MDClub\Service\Report;

/**
 * 删除举报
 */
class Delete extends Abstracts
{
    /**
     * 删除举报
     *
     * @param string $reportableType
     * @param int    $reportableId
     */
    public function delete(string $reportableType, int $reportableId): void
    {
        $this->roleService->managerIdOrFail();

        $this->model
            ->where('reportable_type', $reportableType)
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
        $this->roleService->managerIdOrFail();

        if (!$targets) {
            return;
        }

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
            $this->model->where($where)->delete();
        }
    }
}
