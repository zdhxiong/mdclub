<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Controller\Admin\Index;
use MDClub\Initializer\App;
use MDClub\Middleware\NeedInstalled;

/**
 * 管理后台路由
 */
class Admin
{
    public function __construct()
    {
        $slim = App::$slim;

        $slim
            ->get('/admin[/{path:.+}]', Index::class . ':index')
            ->add(NeedInstalled::class);
    }
}
