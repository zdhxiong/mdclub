<?php

declare(strict_types=1);

namespace App\Library\Cache;

use App\Library\Cache\Interfaces\CacheDriverInterface;
use Medoo\Medoo;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\PdoCache;

/**
 * Pdo 缓存
 *
 * Class Pdo
 * @package App\Library\Cache
 */
class Pdo implements CacheDriverInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Pdo constructor.
     *
     * @param ContainerInterface $container
     * @param array              $option
     */
    public function __construct(ContainerInterface $container, array $option)
    {
        $this->container = $container;
        $this->option = $option;
    }

    /**
     * @return CacheInterface
     */
    public function __invoke()
    {
        $databaseConfig = $this->container->get('settings')['database'];

        $pdo = $this->container->get(Medoo::class)->pdo;
        $namespace = '';
        $defaultLifetime = 0;
        $options = [
            'db_table'        => $databaseConfig['prefix'] . 'cache',
            'db_id_col'       => 'name',
            'db_data_col'     => 'value',
            'db_lifetime_col' => 'life_time',
            'db_time_col'     => 'create_time',
        ];

        return new PdoCache($pdo, $namespace, $defaultLifetime, $options);
    }
}
