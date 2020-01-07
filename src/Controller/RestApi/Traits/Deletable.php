<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi\Traits;

use MDClub\Service\Interfaces\DeletableInterface;

/**
 * 可删除。用于 question, answer, article, comment, topic
 */
trait Deletable
{
    /**
     * @inheritDoc
     *
     * @return DeletableInterface
     */
    abstract protected function getServiceInstance();

    /**
     * 永久批量删除
     *
     * @param array $deletableIds
     *
     * @return array
     */
    public function deleteMultiple(array $deletableIds): array
    {
        $service = $this->getServiceInstance();
        $service->deleteMultiple($deletableIds);

        return [];
    }

    /**
     * 永久删除
     *
     * @param $deletableId
     *
     * @return array
     */
    public function delete(int $deletableId): array
    {
        $service = $this->getServiceInstance();
        $service->delete($deletableId);

        return [];
    }

    /**
     * 批量放入回收站
     *
     * @param array $deletableIds
     * @return array
     */
    public function trashMultiple(array $deletableIds): array
    {
        $service = $this->getServiceInstance();

        return $service->trashMultiple($deletableIds);
    }

    /**
     * 放入回收站
     *
     * @param int $deletableId
     *
     * @return array
     */
    public function trash(int $deletableId): array
    {
        $service = $this->getServiceInstance();

        return $service->trash($deletableId);
    }

    /**
     * 批量移出回收站
     *
     * @param array $deletableIds
     *
     * @return array
     */
    public function untrashMultiple(array $deletableIds): array
    {
        $service = $this->getServiceInstance();

        return $service->untrashMultiple($deletableIds);
    }

    /**
     * 移出回收站
     *
     * @param int $deletableId
     *
     * @return array
     */
    public function untrash(int $deletableId): array
    {
        $service = $this->getServiceInstance();

        return $service->untrash($deletableId);
    }
}
