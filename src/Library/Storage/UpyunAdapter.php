<?php

declare(strict_types=1);

namespace App\Library\Storage;

/**
 * 又拍云存储驱动
 *
 * Class UpyunAdapter
 * @package App\Library\Storage
 */
class UpyunAdapter extends \JellyBool\Flysystem\Upyun\UpyunAdapter
{
    /**
     * UpyunAdapter constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $bucket = $options['storage_upyun_bucket'];
        $operator = $options['storage_upyun_operator'];
        $password = $options['storage_upyun_password'];
        $domain = $options['storage_upyun_endpoint'];

        parent::__construct($bucket, $operator, $password, $domain);
    }
}
