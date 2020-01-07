<?php

declare(strict_types=1);

namespace MDClub\Initializer;

/**
 * Facade 抽象类
 */
abstract class Facade
{
    /**
     * 通过魔术方法调用实际类的方法
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments = [])
    {
        return App::createFacade(static::getFacadeAccessor(), $name, $arguments);
    }

    /**
     * 获取实际类的实例
     *
     * @return mixed
     */
    public static function getInstance()
    {
        return App::$container->get(static::getFacadeAccessor());
    }

    /**
     * 获取当前 Facade 在容器中的类名
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor(): string;
}
