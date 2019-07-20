<?php

declare(strict_types=1);

namespace MDClub\Service\Question;

use MDClub\Abstracts\ContainerProperty;
use Psr\Container\ContainerInterface;

abstract class Abstracts extends ContainerProperty
{
    /**
     * @var \MDClub\Model\Question
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->questionModel;
    }
}
