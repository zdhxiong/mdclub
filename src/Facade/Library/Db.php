<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;
use PDOStatement;

/**
 * Db Facade
 *
 * @method static PDOStatement query(string $query, array $map = [])
 * @method static PDOStatement exec($query, $map = [])
 * @method static string       quote(string $string)
 * @method static PDOStatement create(string $table, array $columns, $options = null)
 * @method static PDOStatement drop(string $table)
 * @method static array        select(string $table, array $join, $columns = null, array $where = null)
 * @method static PDOStatement insert(string $table, array $datas)
 * @method static PDOStatement update(string $table, array $data, array $where = null)
 * @method static PDOStatement delete(string $table, array $where)
 * @method static PDOStatement replace(string $table, array $columns, array $where = null)
 * @method static mixed        get(string $table, array $join = null, $columns = null, array $where = null)
 * @method static boolean      has(string $table, array $join, array $where = null)
 * @method static array        rand(string $table, array $join = null, $columns = null, array $where = null)
 * @method static int          count(string $table, array $join = null, string $column = null, array $where = null)
 * @method static int          avg(string $table, array $join, string $column = null, array $where = null)
 * @method static int          max(string $table, array $join, string $column = null, array $where = null)
 * @method static int          min(string $table, array $join, string $column = null, array $where = null)
 * @method static int          sum(string $table, array $join, string $column = null, array $where = null)
 * @method static void         action($actions)
 * @method static int          id()
 * @method static object       debug()
 * @method static array        error()
 * @method static string       last()
 * @method static array        log()
 * @method static array        info()
 */
class Db extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Db::class;
    }
}
