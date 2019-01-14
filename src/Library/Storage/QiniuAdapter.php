<?php

declare(strict_types=1);

namespace App\Library\Storage;

use App\Interfaces\ContainerInterface;

/**
 * 七牛云存储适配器
 *
 * Class QiniuAdapter
 * @package App\Library\Storage
 */
class QiniuAdapter extends \Overtrue\Flysystem\Qiniu\QiniuAdapter
{
    /**
     * QiniuAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options)
    {
        $accessKey = $options['storage_qiniu_access_id'];
        $secretKey = $options['storage_qiniu_access_secret'];
        $bucket = $options['storage_qiniu_bucket'];
        $domain = $options['storage_qiniu_endpoint'];

        parent::__construct($accessKey, $secretKey, $bucket, $domain);
    }
}
