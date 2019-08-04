<?php

declare(strict_types=1);

namespace MDClub\Service\Question;

/**
 * 删除提问
 */
class Delete extends Abstracts
{
    /**
     * 软删除提问
     *
     * @param int $questionId
     */
    public function delete(int $questionId): void
    {
        $this->model->delete($questionId);
    }

    /**
     * 批量软删除提问
     *
     * @param array $questionIds
     */
    public function deleteMultiple(array $questionIds): void
    {
        if (!$questionIds) {
            return;
        }

        $this->model->delete($questionIds);
    }

    /**
     * 恢复提问
     *
     * @param int $questionId
     */
    public function restore(int $questionId): void
    {
        $this->model->restore($questionId);
    }

    /**
     * 批量恢复提问
     *
     * @param array $questionIds
     */
    public function restoreMultiple(array $questionIds): void
    {
        if (!$questionIds) {
            return;
        }

        $this->model->restore($questionIds);
    }

    /**
     * 硬删除提问
     *
     * @param int $questionId
     */
    public function destroy(int $questionId): void
    {

    }

    /**
     * 批量硬删除提问
     *
     * @param array $questionIds
     */
    public function destroyMultiple(array $questionIds): void
    {

    }
}
