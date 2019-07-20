<?php

declare(strict_types=1);

namespace MDClub\Service;

use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片抽象类
 */
abstract class ImageAbstract extends Abstracts
{
    /**
     * 图片尺寸
     *
     * @return array
     */
    protected function getSize(): array
    {
        return [
            't' => [132, 88],
            'r' => [650, 0],
        ];
    }

    /**
     * 获取图片存储的相对路径
     *
     * @param  string $key
     * @param  int    $timestamp
     * @return string
     */
    protected function getPath(string $key, int $timestamp): string
    {
        $path = implode('/', ['image', date('Y-m/d', $timestamp), substr($key, 0, 2)]);

        return "{$path}/{$key}";
    }

    /**
     * 获取图片后缀名
     *
     * @param  UploadedFileInterface $file
     * @return string
     */
    protected function getSuffix(UploadedFileInterface $file): string
    {
        switch ($file->getClientMediaType()) {
            case 'image/gif':
                $suffix = 'gif';
                break;
            case 'image/png':
                $suffix = 'png';
                break;
            default:
                $suffix = 'jpg';
                break;
        }

        return $suffix;
    }
}
