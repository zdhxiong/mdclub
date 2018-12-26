<?php

declare(strict_types=1);

namespace App\Library\Storage;

use Psr\Container\ContainerInterface;

/**
 * FTP 适配器
 *
 * Class FtpAdapter
 * @package App\Library\Storage
 */
class FtpAdapter extends \League\Flysystem\Adapter\Ftp
{
    /**
     * FtpAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options)
    {
        $config = [
            'host'     => $options['storage_ftp_host'],
            'username' => $options['storage_ftp_username'],
            'password' => $options['storage_ftp_password'],

            /** optional config settings */
            'port'     => $options['storage_ftp_port'],
            'root'     => $options['storage_ftp_root'],
            'passive'  => !!$options['storage_ftp_passive'],
            'ssl'      => !!$options['storage_ftp_ssl'],
            'timeout'  => $options['storage_ftp_timeout'],
        ];

        parent::__construct($config);
    }
}
