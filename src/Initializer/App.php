<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Symfony\Component\Filesystem\Filesystem;

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
     * @param array                       $focusConfig
     */
    public function __construct(ServerRequestInterface $request = null, array $focusConfig = [])
    {
        $config = $this->getConfig($focusConfig);

        // 确保临时文件目录存在（Fast-Route 不会自动创建缓存文件目录）
        if (!is_dir($config['APP_RUNTIME'])) {
            (new Filesystem())->mkdir($config['APP_RUNTIME']);
        }

        // 手动指定响应工厂，无需再自动检测，提升性能
        AppFactory::setResponseFactory(new ResponseFactory());

        $container = new Container($config);

        if (!$request) {
            $request = ServerRequestFactory::createFromGlobals();
        }

        $this->request = $request;
        $container->offsetSet('request', $request);

        AppFactory::setContainer($container);

        $this->app = AppFactory::create();

        new Error($this->app);
        new Dependencies($this->app);
        new Middleware($this->app);
        new Route($this->app);
    }

    /**
     * 获取配置信息
     *
     * @param  array $focusConfig
     * @return array
     */
    protected function getConfig(array $focusConfig): array
    {
        $config = require __DIR__ . '/../../config.default.php';

        if (is_file(__DIR__ . '/../../config.php')) {
            $config = array_merge($config, require __DIR__ . '/../../config.php');
        }

        return array_merge($config, $focusConfig);
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
