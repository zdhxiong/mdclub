<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Followable;
use Psr\Container\ContainerInterface;

/**
 * 话题关注
 */
class TopicFollow extends Abstracts
{
    use Followable;

    /**
     * @var \MDClub\Model\Topic
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->topicModel;
    }
}
