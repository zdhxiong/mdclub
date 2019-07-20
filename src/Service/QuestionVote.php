<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Votable;
use Psr\Container\ContainerInterface;

/**
 * 提问投票
 */
class QuestionVote extends Abstracts
{
    use Votable;

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
