<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Initializer\Facade;
use MDClub\Model\Abstracts as ModelAbstracts;

/**
 * 模型 Facade 抽象类
 *
 * @method static int            max()
 * @method static int            min()
 * @method static int            avg()
 * @method static int            sum()
 * @method static ModelAbstracts force()
 * @method static ModelAbstracts withTrashed()
 * @method static ModelAbstracts onlyTrashed()
 * @method static ModelAbstracts field($columns, bool $exclude = false)
 * @method static ModelAbstracts limit($limit = null)
 * @method static ModelAbstracts order($order = [], string $sort = null)
 * @method static ModelAbstracts match(array $match = null)
 * @method static ModelAbstracts group(array $group = null)
 * @method static ModelAbstracts having(array $having = null)
 * @method static ModelAbstracts join(array $join = null)
 * @method static ModelAbstracts where($where = [], $value = null)
 * @method static ModelAbstracts inc(string $field, int $step = 1)
 * @method static ModelAbstracts dec(string $field, int $step = 1)
 * @method static ModelAbstracts set($data, $value = null)
 * @method static array          select($primaryValues = null)
 * @method static array          pluck(string $value, string $key = null)
 * @method static array          paginate($simple = false)
 * @method static string|null    insert(array $data_array)
 * @method static int            update($data = [], $value = null)
 * @method static int            delete($primaryValues = null)
 * @method static int            restore($primaryValues = null)
 * @method static array|null     get($primaryValue = null)
 * @method static bool           has($primaryValue = null)
 * @method static int            count($reset = true)
 * @method static array          getOrderFromRequest(array $defaultOrder = [])
 * @method static array          getWhereFromRequest(array $defaultFilter = [])
 */
abstract class Abstracts extends Facade
{

}
