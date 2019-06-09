<?php

declare(strict_types=1);

namespace App\Library;

use Medoo\Medoo;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * ORM
 */
class Db extends Medoo
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get('settings')['database'];
        $options = [
            'database_type' => $config['driver'],
            'server'        => $config['host'],
            'database_name' => $config['database'],
            'username'      => $config['username'],
            'password'      => $config['password'],
            'charset'       => $config['charset'],
            'port'          => $config['port'],
            'prefix'        => $config['prefix'],
            'logging'       => APP_DEBUG,
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
