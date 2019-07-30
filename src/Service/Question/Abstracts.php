<?php

declare(strict_types=1);

namespace MDClub\Service\Question;

use MDClub\Service\Abstracts as ServiceAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 提问抽象类
 */
abstract class Abstracts extends ServiceAbstracts
{
    /**
     * @var \MDClub\Model\Question
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->questionModel;
    }
}
