<?php

declare(strict_types=1);

namespace MDClub\Service\Report;

use MDClub\Abstracts\ContainerProperty;
use Psr\Container\ContainerInterface;

/**
 * 举报抽象类
 */
abstract class Abstracts extends ContainerProperty
{
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
}
