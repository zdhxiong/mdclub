<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Tightenco\Collect\Support\Collection as LaravelCollection;

/**
 * 对 Laravel Collection 进行扩展
 */
class Collection extends LaravelCollection
{
    /**
     * 移除二维数组中的指定键
     *
     * $arr = [
     *     ['year' => 2010, 'type' => 1],
     *     ['year' => 2011, 'type' => 2]
     * ]
     * collect($arr)->exceptSpread(['type']) // [ ['year' => 2010], ['year' => 2011] ]
     *
     * @param  Collection|mixed $keys
     * @return static
     */
    public function exceptSpread($keys): self
    {
        return $this->map(function ($item) use ($keys) {
            return collect($item)->except($keys)->all();
        });
    }

    /**
     * 根据键名生成空数组，并添加进原集合。键名重复的，不添加
     *
     * $arr = [
     *     0 => ['year' => 2010],
     *     3 => ['year' => 2013]
     * ]
     * collect($arr)->unionFill([0, 1, 2, 3]) // [ 0 => ['year' => 2010], 1 => [], 2 => [], 3 => ['year' => 2013] ]
     *
     * @param  Collection|array $keys
     * @param                   $defaultValue
     * @return Collection
     */
    public function unionFill($keys, $defaultValue = []): self
    {
        $keys = self::unwrap($keys);

        return $this->union(array_combine($keys, array_fill(0, count($keys), $defaultValue)));
    }
}
