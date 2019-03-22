<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Abstracts\ContainerAbstracts;
use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;

/**
 * 又拍云适配器
 *
 * Class Upyun
 * @package App\Library\Storage\Adapter
 */
class Upyun extends ContainerAbstracts implements StorageInterface
{
    /**
     * Upyun constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);
    }

    /**
     * 写入文件
     *
     * @param  string $path
     * @param  string $content
     * @return bool
     */
    public function write(string $path, string $content): bool
    {
        // TODO: Implement write() method.
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        // TODO: Implement delete() method.
    }

    public function deleteMultiple(array $paths): bool
    {
        // TODO: Implement deleteMultiple() method.
    }
}
