<?php

declare(strict_types=1);

namespace MDClub\Service\User;

use MDClub\Service\Abstracts as ServiceAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 用户抽象类
 */
abstract class Abstracts extends ServiceAbstracts
{
    /**
     * @var \MDClub\Model\User
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->userModel;
    }
}
