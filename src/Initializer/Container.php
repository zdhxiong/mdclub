<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;

/**
 * 容器
 */
class Container extends PimpleContainer implements ContainerInterface
{
    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->offsetExists($id);
    }

    /**
     * 添加一个类到容器中，并以类名作为键名
     *
     * @param string $name
     */
    public function add(string $name): void
    {
        $this->offsetSet($name, function () use ($name) {
            return new $name();
        });
    }
}
