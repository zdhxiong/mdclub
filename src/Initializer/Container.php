<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * 容器
 */
class Container extends PimpleContainer implements ContainerInterface
{
    public function __construct()
    {
        $config = require __DIR__ . '/../../config.php';
        $settings = [
            // 是否是调试模式。调试模式将显示详细错误信息
            'debug' => $config['APP_DEBUG'],

            // 数据库信息
            'database' => [
                'driver'    => $config['DB_CONNECTION'],
                'host'      => $config['DB_HOST'],
                'port'      => $config['DB_PORT'],
                'database'  => $config['DB_DATABASE'],
                'username'  => $config['DB_USERNAME'],
                'password'  => $config['DB_PASSWORD'],
                'charset'   => $config['DB_CHARSET'],
                'prefix'    => $config['DB_PREFIX'],
            ]
        ];

        parent::__construct([ 'settings' => $settings ]);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->offsetExists($id);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }
}
