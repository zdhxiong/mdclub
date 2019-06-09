<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 用户抽象类
 */
abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\User
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->userModel;
    }
}
