<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 文件存储接口
 *
 * 所有文件都需要保留原图
 * 若图片存储服务支持实时图片处理，则不需要预先裁剪图片，在获取图片时添加参数即可；若不支持，则需预先裁剪好所需的缩略图
 */
interface StorageInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * 获取文件的访问路径
     *
     * @param  string  $path    写入时的文件路径
     * @param  array   $thumbs  缩略图尺寸
     * [
     *     's' => [360, 202],  // 宽360px，高202px
     *     'm' => [720, 404],
     *     'l' => [1080, 606],
     *     't' => [650, 0],    // height为0时，表示高度自适应
     * ]
     * @return array
     */
    public function get(string $path, array $thumbs): array;

    /**
     * 写入文件
     *
     * @param  string          $path    写入的文件路径
     * @param  StreamInterface $stream  文件流
     * @param  array           $thumbs  缩略图尺寸
     * [
     *     's' => [360, 202],  // 宽360px，高202px
     *     'm' => [720, 404],
     *     'l' => [1080, 606],
     * ]
     * @return bool
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): bool;

    /**
     * 删除文件
     *
     * @param  string $path    文件路径
     * @param  array  $thumbs  缩略图尺寸
     * @return bool
     */
    public function delete(string $path, array $thumbs): bool;
}
