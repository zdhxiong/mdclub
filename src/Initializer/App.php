<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * 应用入口
 */
class App
{
    /**
     * @param ServerRequestInterface|null $request
     */
    public function __construct(ServerRequestInterface $request = null)
    {
        $container = new Container();

        if (!$request) {
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $request = $serverRequestCreator->createServerRequestFromGlobals();
        }

        $container->offsetSet('request', $request);

        AppFactory::setContainer($container);
        AppFactory::setCallableResolver(new CallableResolver($container));

        $app = AppFactory::create();

        new Error($app);
        new Dependencies($app);
        new Middleware($app);
        new Route($app);
        // new EventListener();

        $app->run($request);
    }
}
