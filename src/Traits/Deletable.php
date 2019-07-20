<?php

declare(strict_types=1);

namespace MDClub\Traits;

/**
 * 可删除的对象，含软删除功能。用于 answer, comment, article, question
 */
trait Deletable
{
    /**
     * 软删除
     *
     * @param int $id
     */
    public function delete(int $id): void
    {

    }

    /**
     * 批量软删除
     *
     * @param array $ids
     */
    public function deleteMultiple(array $ids): void
    {

    }

    /**
     * 恢复
     *
     * @param int $id
     */
    public function restore(int $id): void
    {

    }

    /**
     * 批量恢复
     *
     * @param array $ids
     */
    public function restoreMultiple(array $ids): void
    {

    }

    /**
     * 硬删除
     *
     * @param int $id
     */
    public function destroy(int $id): void
    {

    }

    /**
     * 批量硬删除
     *
     * @param array $ids
     */
    public function destroyMultiple(array $ids): void
    {

    }
}
