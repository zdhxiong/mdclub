<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Initializer\App;
use MDClub\Model\Abstracts as ModelAbstracts;

/**
 * 服务抽象类
 */
abstract class Abstracts
{
    /**
     * 获取当前服务对应的模型实例
     *
     * @return ModelAbstracts
     */
    public function getModelInstance(): ModelAbstracts
    {
        return App::$container->get($this->getModel());
    }

    /**
     * 获取当前服务对应的模型名称
     *
     * @return string
     */
    abstract protected function getModel(): string;
}
