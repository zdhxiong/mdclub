<?php

declare(strict_types=1);

namespace App\Library\Storage;

use JellyBool\Flysystem\Upyun\UpyunAdapter;

/**
 * 又拍云存储驱动
 *
 * Class Upyun
 * @package App\Library\Storage
 */
class Upyun implements StorageInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Upyun constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 获取适配器实例
     *
     * @return UpyunAdapter
     */
    public function getAdapter(): UpyunAdapter
    {
        return new UpyunAdapter(
            $this->option['storage_upyun_bucket'],
            $this->option['storage_upyun_operator'],
            $this->option['storage_upyun_password'],
            $this->option['storage_upyun_endpoint']
        );
    }
}
