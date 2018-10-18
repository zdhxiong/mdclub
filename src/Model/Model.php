<?php

declare(strict_types=1);

namespace App\Model;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Medoo\Medoo;

/**
 * Class Model
 *
 * @method max(): int
 * @method min(): int
 * @method avg(): int
 * @method sum(): int
 *
 * @package App\Model
 */
class Model
{
    /**
     * 自动维护的 create_time 字段名
     */
    const CREATE_TIME = 'create_time';

    /**
     * 自动维护的 update_time 字段名
     */
    const UPDATE_TIME = 'update_time';

    /**
     * 软删除字段名
     */
    const DELETE_TIME = 'delete_time';

    /**
     * 容器实例
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Medoo 数据库实例
     *
     * @var Medoo
     */
    protected $database;

    /**
     * 请求实例
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * 表名
     *
     * @var string
     */
    protected $table;

    /**
     * 主键列名
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * 表的所有字段名
     *
     * @var array
     */
    protected $columns;

    /**
     * 是否启用软删除
     *
     * @var bool
     */
    protected $softDelete = false;

    /**
     * 是否自动维护 create_time 和 update_time 字段
     *
     * @var bool
     */
    protected $timestamps = false;

    private $force;           // 删除时是否强制删除
    private $withTrashed;     // 查询结果包含软删除值
    private $onlyTrashed;     // 查询结果仅含软删除值
    private $order;           // 排序
    private $limit;           // limit
    private $where;           // where
    private $match;           // 全文检索
    private $group;           // group
    private $having;          // having
    private $join;            // join
    private $column;          // 查询字段
    private $isColumnExclude; // 是否排除查询字段

    /**
     * 恢复默认状态
     */
    private function reset()
    {
        $this->force = false;
        $this->withTrashed = false;
        $this->onlyTrashed = false;
        $this->order = null;
        $this->limit = null;
        $this->where = [];
        $this->match = null;
        $this->group = null;
        $this->having = null;
        $this->join = null;
        $this->column = $this->columns;
        $this->isColumnExclude = false;
    }

    /**
     * Model constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->database = $container->get(Medoo::class);
        $this->request = $container->get('request');
        $this->reset();
    }

    /**
     * 忽略软删除
     *
     * @return $this
     */
    public function force(): self
    {
        $this->force = true;

        return $this;
    }

    /**
     * 在结果中包含软删除的结果
     *
     * @return $this
     */
    public function withTrashed(): self
    {
        $this->withTrashed = true;

        return $this;
    }

    /**
     * 只返回软删除的结果
     *
     * @return $this
     */
    public function onlyTrashed(): self
    {
        $this->onlyTrashed = true;

        return $this;
    }

    /**
     * columns
     *
     * @param  array|string  $columns    字段数组
     * @param  bool          $exclude  是否为排除字段
     * @return Model
     */
    public function field($columns, bool $exclude = false): self
    {
        $this->column = $columns;
        $this->isColumnExclude = $exclude;

        return $this;
    }

    /**
     * limit
     *
     * @param  int|array $limit
     * @return Model
     */
    public function limit($limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * order
     *
     * @param  string|array  $order
     * @return Model
     */
    public function order($order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * match
     *
     * @param  array $match
     * @return Model
     */
    public function match(array $match): self
    {
        $this->match = $match;

        return $this;
    }

    /**
     * group
     *
     * @param  string  $group
     * @return Model
     */
    public function group(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * having
     *
     * @param  array  $having
     * @return Model
     */
    public function having(array $having): self
    {
        $this->having = $having;

        return $this;
    }

    /**
     * join
     *
     * @param $join
     * @return Model
     */
    public function join($join): self
    {
        $this->join = $join;

        return $this;
    }

    /**
     * where
     *
     * @param array $where
     * @return Model
     */
    public function where(array $where): self
    {
        $this->where = $where;

        return $this;
    }

    /**
     * 获取供 medoo 使用的 where 参数
     *
     * @param  array  $where
     * @return array
     */
    private function getWhere(array $where = null)
    {
        if (!$where) {
            $where = $this->where;
        }

        $map = [
            'ORDER' => $this->order,
            'MATCH' => $this->match,
            'GROUP' => $this->group,
            'HAVING' => $this->having,
            'LIMIT' => $this->limit,
        ];

        foreach ($map as $name => $value) {
            if ($value) {
                $where[$name] = $value;
            }
        }

        if ($this->softDelete && !$this->force) {
            $deleteTimeField = $this->join
                ? $this->table . '.' . static::DELETE_TIME
                : static::DELETE_TIME;

            $where[$deleteTimeField] = 0;

            if ($this->withTrashed || $this->onlyTrashed) {
                unset($where[$deleteTimeField]);
            }

            if ($this->onlyTrashed) {
                $where[$deleteTimeField . '[>]'] = 0;
            }
        }

        return $where;
    }

    /**
     * 获取要查询的列
     *
     * @return array
     */
    private function getColumns()
    {
        $columns = $this->column;
        $isColumnExclude = $this->isColumnExclude;

        if (is_string($columns)) {
            $columns = trim($columns);
        }

        if ($columns && is_string($columns)) {
            $columns = explode(',', $columns);
            $columns = array_map(function ($item) {
                return trim($item);
            }, $columns);
        }

        if (!$columns) {
            $columns = $this->columns;

            if ($isColumnExclude) {
                $isColumnExclude = false;
            }
        }

        if ($isColumnExclude) {
            $columnExclude = $columns;
            $columns = $this->columns;

            foreach ($columns as $key => $item) {
                if (in_array($item, $columnExclude)) {
                    unset($columns[$key]);
                }
            }
        }

        return $columns;
    }

    /**
     * 插入数据前对数据进行处理
     *
     * @param  array $data 处理前的数据
     * @return array       处理后的数据
     */
    protected function beforeInsert(array $data): array
    {
        return $data;
    }

    /**
     * 更新数据前对数据进行处理
     *
     * @param  array $data 处理前的数据
     * @return array       处理后的数据
     */
    protected function beforeUpdate(array $data): array
    {
        return $data;
    }

    /**
     * 根据条件获取多条数据
     *
     * @param  array  $primaryValues 若传入了该参数，则根据主键值数组获取，否则根据前面的 where 条件获取
     * @return array
     */
    public function select(array $primaryValues = null): array
    {
        $join = $this->join;
        $columns = $this->getColumns();
        $where = $this->getWhere($primaryValues ? [$this->primaryKey => $primaryValues] : null);

        $this->reset();

        if ($join) {
            return $this->database->select($this->table, $join, $columns, $where);
        } else {
            return $this->database->select($this->table, $columns, $where);
        }
    }

    /**
     * 获取包含单列值的数组
     *
     * @param  string $column 列名
     * @return array
     */
    public function pluck(string $column): array
    {
        $this->field($column);
        $result = $this->select();

        return array_column($result, $column);
    }

    /**
     * 根据条件获取带分页的数据
     *
     * @param  bool|int $simple 是否使用简单分页（简单分页不含总数据量）；已知数据总量的情况下，可以传入数据总量
     * @return array
     */
    public function paginate($simple = false): array
    {
        /** @var Request $request */
        $request = $this->container->get('request');

        if (is_int($simple)) {
            $total = $simple;
            $simple = false;
        }

        // page 参数
        $page = intval($request->getQueryParam('page', 1));
        if ($page < 1) {
            $page = 1;
        }

        // per_page 参数
        $perPage = intval($request->getQueryParam('per_page', 15));
        if ($perPage > 100) {
            $perPage = 100;
        } elseif ($perPage < 1) {
            $perPage = 1;
        }

        $this->limit([$perPage * ($page - 1), $perPage]);

        if (!$simple && !isset($total)) {
            $total = $this->count(false);
        }

        $result = [
            'data' => $this->select(),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'from' => $perPage * ($page - 1) + 1,
                'to' => $perPage * $page,
            ],
        ];

        if (isset($total)) {
            $result['pagination']['total'] = $total;
            $result['pagination']['total_page'] = ceil($total / $perPage);
        }

        return $result;
    }

    /**
     * 插入数据
     *
     * @param  array   $data_array 数据数组、或多个数据组成的二维数组
     * @return int|string
     */
    public function insert(array $data_array)
    {
        if (!isset($data_array[0])) {
            $data_array = [$data_array];
        }

        foreach ($data_array as &$data) {
            $data = $this->beforeInsert($data);

            if ($this->timestamps && static::CREATE_TIME) {
                $data[static::CREATE_TIME] = $this->request->getServerParams()['REQUEST_TIME'];
            }

            if ($this->timestamps && static::UPDATE_TIME) {
                $data[static::UPDATE_TIME] = $this->request->getServerParams()['REQUEST_TIME'];
            }

            if ($this->softDelete && static::DELETE_TIME) {
                $data[static::DELETE_TIME] = 0;
            }
        }

        $this->database->insert($this->table, $data_array);

        return $this->database->id();
    }

    /**
     * 更新数据
     *
     * @param  array $data    需要更新的数据
     * @return int
     */
    public function update(array $data): int
    {
        $data = $this->beforeUpdate($data);

        if ($this->timestamps && static::UPDATE_TIME) {
            $data[static::UPDATE_TIME] = $this->request->getServerParams()['REQUEST_TIME'];
        }

        $where = $this->getWhere();

        $this->reset();

        $query = $this->database->update($this->table, $data, $where);

        return $query->rowCount();
    }

    /**
     * 根据条件删除数据
     *
     * @param  int|string|array    $primaryValues 若传入该参数，则根据主键删除，否则根据前面的 where 条件删除
     * @return int
     */
    public function delete($primaryValues = null): int
    {
        $force = $this->force;
        $join = $this->join;
        $where = $this->getWhere($primaryValues ? [$this->primaryKey => $primaryValues] : null);

        $this->reset();

        if ($this->softDelete && !$force) {
            $deleteTimeField = $join
                ? $this->table . '.' . static::DELETE_TIME
                : static::DELETE_TIME;

            $query = $this->database->update($this->table, [
                $deleteTimeField => $this->request->getServerParams()['REQUEST_TIME']
            ], $where);
        } else {
            $query = $this->database->delete($this->table, $where);
        }

        return $query->rowCount();
    }

    /**
     * 根据条件获取一条数据
     *
     * @param  int|string  $primaryValue 若传入了该参数，则根据主键获取，否则根据前面的 where 参数获取
     * @return array|bool
     */
    public function get($primaryValue = null)
    {
        $join = $this->join;
        $columns = $this->getColumns();
        $where = $this->getWhere($primaryValue ? [$this->primaryKey => $primaryValue] : null);

        $this->reset();

        if ($join) {
            return $this->database->get($this->table, $join, $columns, $where);
        } else {
            return $this->database->get($this->table, $columns, $where);
        }
    }

    /**
     * 判断数据是否存在
     *
     * @param  int|string  $primaryValue  若传入了该参数，则根据主键获取，否则根据前面的 where 参数获取
     * @return bool
     */
    public function has($primaryValue = null): bool
    {
        $join = $this->join;
        $where = $this->getWhere($primaryValue ? [$this->primaryKey => $primaryValue] : null);

        $this->reset();

        if ($join) {
            return $this->database->has($this->table, $join, $where);
        } else {
            return $this->database->has($this->table, $where);
        }
    }

    /**
     * 查询总数
     *
     * @param  bool $reset 查询完是否重置
     * @return int
     */
    public function count($reset = true): int
    {
        $join = $this->join;
        $columns = $this->getColumns();
        $where = $this->getWhere();

        unset($where['ORDER'], $where['LIMIT']);

        if ($reset) {
            $this->reset();
        }

        if ($join) {
            return $this->database->count($this->table, $join, $columns, $where);
        } else {
            return $this->database->count($this->table, $where);
        }
    }

    /**
     * @param  $name
     * @param  $arguments
     * @return int
     */
    public function __call($name, $arguments)
    {
        $aggregation = ['avg', 'max', 'min', 'sum'];

        if (in_array($name, $aggregation)) {
            $join = $this->join;
            $columns = $this->getColumns();
            $where = $this->getWhere();

            $this->reset();

            if ($join) {
                return call_user_func_array([$this, $name], [$this->table, $join, $columns, $where]);
            } else {
                return call_user_func_array([$this, $name], [$this->table, $columns, $where]);
            }
        } else {
            throw new \BadMethodCallException();
        }
    }
}
