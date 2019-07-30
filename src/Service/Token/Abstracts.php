<?php

declare(strict_types=1);

namespace MDClub\Service\Token;

use MDClub\Service\Abstracts as ServiceAbstracts;
use Psr\Container\ContainerInterface;

/**
 * Token 抽象类
 */
abstract class Abstracts extends ServiceAbstracts
{
    /**
     * @var \MDClub\Model\Token
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->tokenModel;
    }
}
