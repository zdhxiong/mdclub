<?php

declare(strict_types=1);

namespace App\Library\Storage;

/**
 * 存储驱动的接口
 *
 * Interface StorageInterface
 * @package App\Library\Storage
 */
interface StorageInterface
{
    /**
     * StorageInterface constructor.
     * @param array $option
     */
    public function __construct(array $option);

    /**
     * 获取适配器实例
     *
     * @return mixed
     */
    public function getAdapter();
}
