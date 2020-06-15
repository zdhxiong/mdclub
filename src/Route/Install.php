<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Constant\RouteNameConstant;
use MDClub\Controller\Install\Index;
use MDClub\Initializer\App;

/**
 * Install 路由
 */
class Install
{
    public function __construct()
    {
        $slim = App::$slim;

        $slim
            ->get('/install', Index::class . ':index')
            ->setName(RouteNameConstant::INSTALL);
    }
}
