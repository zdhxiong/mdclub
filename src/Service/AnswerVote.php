<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Votable;
use Psr\Container\ContainerInterface;

/**
 * 回答投票
 */
class AnswerVote extends Abstracts
{
    use Votable;

    /**
     * @var \MDClub\Model\Answer
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->answerModel;
    }
}
