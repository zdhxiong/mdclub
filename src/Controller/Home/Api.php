<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * API
 */
class Api
{
    /**
     * API 接口页面
     *
     * todo 输出所有 api 的描述信息的 json
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/api.php');
    }
}
