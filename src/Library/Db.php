<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Initializer\App;
use Medoo\Medoo;
use PDO;

/**
 * ORM
 */
class Db extends Medoo
{
    /**
     * @param array $customConfig
     */
    public function __construct(array $customConfig = [])
    {
        $config = array_merge(App::$config, $customConfig);
        $options = [
            'database_type' => $config['DB_CONNECTION'],
            'server'        => $config['DB_HOST'],
            'database_name' => $config['DB_DATABASE'],
            'username'      => $config['DB_USERNAME'],
            'password'      => $config['DB_PASSWORD'],
            'charset'       => $config['DB_CHARSET'],
            'port'          => $config['DB_PORT'],
            'prefix'        => $config['DB_PREFIX'],
            'logging'       => $config['APP_DEBUG'],
            'option'        => [
                PDO::ATTR_CASE               => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ];

        parent::__construct($options);
    }
}
