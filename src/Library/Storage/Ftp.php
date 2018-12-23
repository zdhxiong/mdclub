<?php

declare(strict_types=1);

namespace App\Library\Storage;

use League\Flysystem\Adapter\Ftp as FtpAdapter;

/**
 * FTP 存储驱动
 *
 * Class Ftp
 * @package App\Library\Storage
 */
class Ftp implements StorageInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Ftp constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 获取适配器实例
     *
     * @return FtpAdapter
     */
    public function getAdapter(): FtpAdapter
    {
        return new FtpAdapter([
            'host'     => $this->option['storage_ftp_host'],
            'username' => $this->option['storage_ftp_username'],
            'password' => $this->option['storage_ftp_password'],

            /** optional config settings */
            'port'     => $this->option['storage_ftp_port'],
            'root'     => $this->option['storage_ftp_root'],
            'passive'  => !!$this->option['storage_ftp_passive'],
            'ssl'      => !!$this->option['storage_ftp_ssl'],
            'timeout'  => $this->option['storage_ftp_timeout'],
        ]);
    }
}
