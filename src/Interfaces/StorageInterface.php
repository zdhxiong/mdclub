<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * 文件存储接口
 *
 * Interface StorageInterface
 * @package App\Interfaces
 */
interface StorageInterface
{
    /**
     * StorageInterface constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container);

    /**
     * 写入文件
     *
     * @param  string $path    文件路径
     * @param  string $content 文件内容
     * @return bool
     */
    public function write(string $path, string $content): bool;

    /**
     * 删除文件
     *
     * @param  string $path 文件路径
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * 批量删除多个文件
     *
     * @param  array $paths 多个文件路径组成的数组
     * @return bool
     */
    public function deleteMultiple(array $paths): bool;
}
