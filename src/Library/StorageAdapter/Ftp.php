<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Abstracts\ContainerAbstracts;
use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;

/**
 * FTP/FTPS 适配器
 *
 * NOTE: FTPS 需要开启 openssl 扩展
 *
 * Class Ftp
 * @package App\Library\Storage\Adapter
 */
class Ftp extends ContainerAbstracts implements StorageInterface
{
    /**
     * @var resource FTP 链接实例
     */
    protected $connect;

    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * Ftp constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->setPathPrefix();

        [
            'storage_ftp_username' => $username,
            'storage_ftp_password' => $password,
            'storage_ftp_host' => $host,
            'storage_ftp_port' => $port,
            'storage_ftp_ssl' => $ssl,
        ] = $container->optionService->getMultiple();

        $this->connect = $ssl
            ? ftp_ssl_connect($host, (int)$port)
            : ftp_connect($host, (int)$port);

        ftp_login($this->connect, $username, $password);
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = $this->container->optionService->storage_ftp_root;

        if ($prefix && !in_array(substr($prefix, -1), ['/', '\\'])) {
            $prefix .= '/';
        }

        $this->pathPrefix = $prefix;
    }

    /**
     * 获取包含文件路径的文件存储地址
     *
     * @param  string $path
     * @return string
     */
    protected function applyPathPrefix(string $path): string
    {
        return $this->pathPrefix . ltrim($path, '\\/');
    }

    /**
     * 确保指定目录存在，若不存在，则创建指定目录
     *
     * @param string $root
     */
    protected function ensureDirectory(string $root): void
    {
        $parts = explode('/', $root);

        foreach ($parts as $part) {
            if (!@ftp_chdir($this->connect, $part)) {
                ftp_mkdir($this->connect, $part);
                ftp_chmod($this->connect, 0755, $part);
                ftp_chdir($this->connect, $part);
            }
        }
    }

    /**
     * 写入文件
     *
     * @param  string $path
     * @param  string $tmp_path
     * @return bool
     */
    public function write(string $path, string $tmp_path): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        return ftp_put($this->connect, $location, $tmp_path, FTP_BINARY);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        return @ftp_delete($this->connect, $location);
    }

    /**
     * 批量删除文件
     *
     * @param  array $paths
     * @return bool
     */
    public function deleteMultiple(array $paths): bool
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }

        return true;
    }

    /**
     * 析构方法，断开 FTP 连接
     */
    public function __destruct()
    {
        ftp_close($this->connect);
    }
}
