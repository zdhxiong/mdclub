<?php

declare(strict_types=1);

namespace App\Service\Question;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;

abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\Question
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->questionModel;
    }
}
