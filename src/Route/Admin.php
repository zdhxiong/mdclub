<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Controller\Admin\Index;
use Slim\App;

/**
 * 管理后台路由
 */
class Admin
{
    public function __construct(App $app)
    {
        /**
         * 后台管理首页
         *
         * @see Index::index()
         */
        $app->get('/admin[/{path}]', Index::class . ':index');
    }
}
