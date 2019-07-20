<?php

declare(strict_types=1);

namespace MDClub\Observer;

use Psr\Container\ContainerInterface;

/**
 * 观察者抽象类
 */
abstract class Abstracts
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * 创建单条记录后
     *
     * 仅创建单条记录有事件，创建多条记录没有事件
     *
     * @param array $item
     */
    public function created(array $item): void
    {

    }

    /**
     * 更新单条记录后
     *
     * @param mixed $key 主键
     */
    public function updated($key): void
    {

    }

    /**
     * 更新多条记录后
     *
     * @param array $keys
     */
    public function updatedMultiple(array $keys): void
    {

    }

    /**
     * 软删除单条记录后
     *
     * @param mixed $key
     */
    public function deleted($key): void
    {

    }

    /**
     * 软删除多条记录后
     *
     * @param array $keys
     */
    public function deletedMultiple(array $keys): void
    {

    }

    /**
     * 恢复单条记录后
     *
     * @param mixed $key
     */
    public function restored($key): void
    {

    }

    /**
     * 恢复多条记录后
     *
     * @param array $keys
     */
    public function restoredMultiple(array $keys): void
    {

    }

    /**
     * 销毁单条记录后
     *
     * @param mixed $key
     */
    public function destroy($key): void
    {

    }

    /**
     * 销毁多条记录后
     *
     * @param array $keys
     */
    public function destroyMultiple(array $keys): void
    {

    }
}
