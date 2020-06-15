<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi\Traits;

use MDClub\Service\Interfaces\GetableInterface;

/**
 * 可获取。用于 question, answer, article, comment, topic, user, image
 */
trait Getable
{
    /**
     * @inheritDoc
     *
     * @return GetableInterface
     */
    abstract protected function getServiceInstance();

    /**
     * 获取对象列表
     *
     * @return array
     */
    public function getList(): array
    {
        $service = $this->getServiceInstance();

        return $service->getList();
    }

    /**
     * 获取对象的详情
     *
     * @param int|string $getableId
     *
     * @return array
     */
    public function get($getableId): array
    {
        $service = $this->getServiceInstance();

        return $service->getOrFail($getableId);
    }
}
