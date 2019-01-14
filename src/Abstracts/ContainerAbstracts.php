<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Interfaces\ContainerInterface;

/**
 * Class ContainerAbstracts
 *
 * @package App\Abstracts
 */
abstract class ContainerAbstracts
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ContainerAbstracts constructor.
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
}
