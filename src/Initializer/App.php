<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

/**
 * 应用入口
 */
class App
{
    /**
     * Slim 应用实例
     *
     * @var \Slim\App
     */
    public static $slim;

    /**
     * 配置文件中的数据
     *
     * @var array
     */
    public static $config;

    /**
     * 容器实例
     *
     * @var Container
     */
    public static $container;

    /**
     * 请求实例
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @param ServerRequestInterface|null $request     可传入实现了 psr7 的请求对象
     * @param array                       $focusConfig 传入该配置，可强制覆盖自定义配置
     */
    public function __construct(ServerRequestInterface $request = null, array $focusConfig = [])
    {
        $this->request = $request ?? ServerRequestFactory::createFromGlobals();
        $container = new Container([ ServerRequestInterface::class => $this->request ]);
        $config = $this->getConfig($focusConfig);

        AppFactory::setResponseFactory(new ResponseFactory());
        AppFactory::setContainer($container);

        $slim = AppFactory::create();

        self::$slim = $slim;
        self::$config = $config;
        self::$container = $container;

        new Error();
        new Dependencies();
        new Route();
    }

    /**
     * 获取配置信息
     *
     * @param  array $focusConfig
     * @return array
     */
    protected function getConfig(array $focusConfig = []): array
    {
        $config = require __DIR__ . '/../../config/config.default.php';

        if (is_file(__DIR__ . '/../../config/config.php')) {
            $config = array_merge($config, require __DIR__ . '/../../config/config.php');
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
        return self::$slim->handle($this->request);
    }

    /**
     * 调用容器中指定类的指定方法
     *
     * @param  string $facadeAccessor
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public static function createFacade(string $facadeAccessor, string $name, array $arguments = [])
    {
        $object = self::$container->get($facadeAccessor);

        return call_user_func_array([$object, $name], $arguments);
    }
}
