<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 举报抽象类
 */
abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\Report
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->reportModel;
    }
}
