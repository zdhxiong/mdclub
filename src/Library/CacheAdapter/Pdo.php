<?php

declare(strict_types=1);

namespace MDClub\Library\CacheAdapter;

use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\PdoAdapter;

/**
 * Pdo 缓存适配器
 */
class Pdo extends PdoAdapter
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $databaseConfig = $container->get('settings')['database'];
        $pdo = $container->get('db')->pdo;
        $namespace = $container->get('option')->cache_prefix;
        $defaultLifetime = 0;
        $config = [
            'db_table'        => $databaseConfig['prefix'] . 'cache',
            'db_id_col'       => 'name',
            'db_data_col'     => 'value',
            'db_lifetime_col' => 'life_time',
            'db_time_col'     => 'create_time',
        ];

        parent::__construct($pdo, $namespace, $defaultLifetime, $config);
    }
}
