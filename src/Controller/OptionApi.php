<?php

declare(strict_types=1);

namespace MDClub\Controller;

use MDClub\Middleware\NeedManager;

/**
 * 系统设置
 *
 * @property-read \MDClub\Library\Option $option
 */
class OptionApi extends Abstracts
{
    /**
     * 获取所有设置项
     *
     * @return array
     */
    public function get(): array
    {
        return $this->option->onlyAuthorized()->all();
    }

    /**
     * 更新设置，仅管理员可操作
     *
     * @uses NeedManager
     * @return array
     */
    public function update(): array
    {
        $this->option->set($this->request->getParsedBody());

        return $this->option->all();
    }
}
