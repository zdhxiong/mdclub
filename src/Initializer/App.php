<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * 应用入口
 */
class App
{
    protected $app;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

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

        $this->request = $request;
        $container->offsetSet('request', $request);

        AppFactory::setContainer($container);
        AppFactory::setCallableResolver(new CallableResolver($container));

        $this->app = AppFactory::create();

        new Error($this->app);
        new Dependencies($this->app);
        new Middleware($this->app);
        new Route($this->app);
        // new EventListener();
    }

    /**
     * 为了适应不同平台，直接返回 ResponseInterface，在 index.php 中进行具体处理
     *
     * @return ResponseInterface
     */
    public function run(): ResponseInterface
    {
        return $this->app->handle($this->request);
    }
}
