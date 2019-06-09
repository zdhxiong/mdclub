<?php

declare(strict_types=1);

namespace App\Traits;

use Tightenco\Collect\Support\Collection;

/**
 * 支持获取 Collection
 */
trait fetchCollection
{
    /**
     * 是否返回 Collection
     *
     * @var bool
     */
    protected $isCollection = false;

    /**
     * 将结果以 Collection 的形式返回
     *
     * @return fetchCollection
     */
    public function fetchCollection(): self
    {
        $this->isCollection = true;

        return $this;
    }

    /**
     * 根据是否调用了 fetchCollection 决定返回 array 还是 Collection
     *
     * @param  array|Collection|null $data
     * @return array|Collection
     */
    public function returnArray($data)
    {
        if ($data instanceof Collection) {
            if (!$this->isCollection) {
                $data = $data->all();
            }
        } elseif ($this->isCollection) {
            $data = collect($data);
        }

        $this->isCollection = false;

        return $data;
    }
}
