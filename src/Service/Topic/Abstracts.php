<?php

declare(strict_types=1);

namespace App\Service\Topic;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 话题抽象类
 */
abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\Topic
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->topicModel;
    }
}
