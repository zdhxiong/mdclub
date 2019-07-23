<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;

/**
 * 举报
 */
class Report extends Abstracts
{
    use Getable;

    /**
     * @var \MDClub\Model\Report
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->reportModel;
    }

    /**
     * 获取举报列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->reportModel->getList();
    }

    public function getReasons(string $reportableType, int $reportableId): array
    {
        // TODO: Implement getDetailList() method.
    }

    public function create(string $reportableType, int $reportableId, string $reason): int
    {
        // TODO: Implement create() method.
    }

    public function delete(string $reportableType, int $reportableId): void
    {
        // TODO: Implement delete() method.
    }

    public function deleteMultiple(array $targets): void
    {
        // TODO: Implement deleteMultiple() method.
    }
}
