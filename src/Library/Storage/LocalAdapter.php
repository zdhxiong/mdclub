<?php

declare(strict_types=1);

namespace App\Library\Storage;

/**
 * 本地存储驱动
 *
 * Class LocalAdapter
 * @package App\Library\Storage
 */
class LocalAdapter extends \League\Flysystem\Adapter\Local
{
    /**
     * LocalAdapter constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $uploadDir = $options['storage_local_dir'];
        if ($uploadDir && !in_array(substr($uploadDir, -1), ['/', '\\'])) {
            $uploadDir .= '/';
        }

        if (!$uploadDir) {
            $uploadDir = __DIR__ . '/../../../public/static/upload/';
        }

        parent::__construct($uploadDir);
    }
}
