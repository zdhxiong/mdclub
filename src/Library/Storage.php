<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Storage\AliyunOSSAdapter;
use App\Library\Storage\FtpAdapter;
use App\Library\Storage\LocalAdapter;
use App\Library\Storage\QiniuAdapter;
use App\Library\Storage\UpyunAdapter;
use App\Service\OptionService;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;

/**
 * 文件存储，仅限图片
 *
 * Class Storage
 * @package App\Library
 */
class Storage extends Filesystem
{
    /**
     * @var OptionService
     */
    protected $optionService;

    /**
     * 存储名称和适配器类名数组
     *
     * @var array
     */
    protected $adapterMap = [
        'local'      => LocalAdapter::class,
        'ftp'        => FtpAdapter::class,
        'aliyun_oss' => AliyunOSSAdapter::class,
        'upyun'      => UpyunAdapter::class,
        'qiniu'      => QiniuAdapter::class,
    ];

    /**
     * Storage constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->optionService = $container->get(OptionService::class);
        $options = $this->optionService->getAll();
        $storageType = $options['storage_type'];

        if (!isset($this->adapterMap[$storageType])) {
            throw new \Exception('不存在指定的存储类型：' . $storageType);
        }

        $adapter = new $this->adapterMap[$storageType]($container, $options);
        $config = ['visibility' => AdapterInterface::VISIBILITY_PUBLIC];

        parent::__construct($adapter, $config);
    }
}
