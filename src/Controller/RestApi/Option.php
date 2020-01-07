<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Library\Option as OptionLibrary;
use MDClub\Facade\Library\Request;

/**
 * 系统设置 API
 */
class Option
{
    /**
     * 获取所有设置项
     *
     * @return array
     */
    public function get(): array
    {
        return OptionLibrary::onlyAuthorized()->getAll();
    }

    /**
     * 更新设置，仅管理员可操作
     *
     * @return array
     */
    public function update(): array
    {
        $requestBody = Request::getParsedBody();
        OptionLibrary::setMultiple($requestBody);

        return OptionLibrary::getAll();
    }
}
