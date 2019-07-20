<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Followable;
use Psr\Container\ContainerInterface;

/**
 * 提问关注
 */
class QuestionFollow extends Abstracts
{
    use Followable;

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
