<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Initializer\App;

/**
 * RestApi 控制器抽象类
 */
abstract class Abstracts
{
    /**
     * 获取当前控制器对应的服务实例
     */
    protected function getServiceInstance()
    {
        return App::$container->get($this->getService());
    }

    /**
     * 获取当前控制器对应的服务名称
     *
     * @return string
     */
    abstract protected function getService(): string;
}
