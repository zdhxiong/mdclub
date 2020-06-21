<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Constant\RouteNameConstant;
use MDClub\Controller\Install\Index;
use MDClub\Initializer\App;
use MDClub\Middleware\NeedNotInstalled;
use Slim\Routing\RouteCollectorProxy;

/**
 * Install 路由
 */
class Install
{
    public function __construct()
    {
        App::$slim
            ->group('/install', function (RouteCollectorProxy $group) {
                $group
                    ->get('', Index::class . ':index')
                    ->setName(RouteNameConstant::INSTALL);

                $group
                    ->post('/import_database', Index::class . ':importDatabase');
            })
            ->add(NeedNotInstalled::class);
    }
}
