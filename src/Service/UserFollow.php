<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Followable;
use Psr\Container\ContainerInterface;

/**
 * 用户关注
 */
class UserFollow extends Abstracts
{
    use Followable;

    /**
     * @var \MDClub\Model\User
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->userModel;
    }
}
