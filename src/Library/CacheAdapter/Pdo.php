<?php

declare(strict_types=1);

namespace MDClub\Library\CacheAdapter;

use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Db;
use MDClub\Facade\Library\Option;
use MDClub\Initializer\App;
use Symfony\Component\Cache\Adapter\PdoAdapter;

/**
 * Pdo 缓存适配器
 */
class Pdo extends PdoAdapter
{
    public function __construct()
    {
        $pdo = Db::getInstance()->pdo;
        $namespace = Option::get(OptionConstant::CACHE_PREFIX);
        $defaultLifetime = 0;
        $config = [
            'db_table'        => App::$config['DB_PREFIX'] . 'cache',
            'db_id_col'       => 'name',
            'db_data_col'     => 'value',
            'db_lifetime_col' => 'life_time',
            'db_time_col'     => 'create_time',
        ];

        parent::__construct($pdo, $namespace, $defaultLifetime, $config);
    }
}
