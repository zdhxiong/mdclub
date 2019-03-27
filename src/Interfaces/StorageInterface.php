<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\StreamInterface;

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
     * @param  string          $path    写入的文件路径
     * @param  StreamInterface $stream  临时文件路径
     * @return bool
     */
    public function write(string $path, StreamInterface $stream): bool;

    /**
     * 删除文件
     *
     * @param  string $path 文件路径
     * @return bool
     */
    public function delete(string $path): bool;
}
