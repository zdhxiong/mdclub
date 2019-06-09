<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Exception\SystemException;
use App\Interfaces\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * FTP/FTPS 适配器
 *
 * NOTE: FTPS 需要开启 openssl 扩展
 */
class Ftp extends AbstractAdapter implements StorageInterface
{
    /**
     * FTP 连接 resource
     *
     * @var resource
     */
    protected $connection;

    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        if (!extension_loaded('ftp')) {
            throw new SystemException('PHP extension FTP is not loaded.');
        }

        $this->setPathPrefix();

        $username = $this->optionService->storage_ftp_username;
        $password = $this->optionService->storage_ftp_password;
        $host = $this->optionService->storage_ftp_host;
        $port = $this->optionService->storage_ftp_port;
        $ssl = $this->optionService->storage_ftp_ssl;
        $passive = $this->optionService->storage_ftp_passive;

        $this->connection = $ssl
            ? ftp_ssl_connect($host, (int) $port)
            : ftp_connect($host, (int) $port);

        if (!$this->connection) {
            throw new SystemException("Couldn't connect to FTP Server ${host}:${port}");
        }

        ftp_login($this->connection, $username, $password);
        ftp_pasv($this->connection, !!$passive);
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = $this->optionService->storage_ftp_root;

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
        $pwd = ftp_pwd($this->connection);
        $parts = explode('/', $root);

        foreach ($parts as $part) {
            if (!$part) {
                continue;
            }

            if (!@ftp_chdir($this->connection, $part)) {
                ftp_mkdir($this->connection, $part);
                ftp_chdir($this->connection, $part);
            }
        }

        ftp_chdir($this->connection, $pwd);
    }

    /**
     * 获取图片 URL
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return array
     */
    public function get(string $path, array $thumbs): array
    {
        $url = $this->urlService->storage();
        $data['o'] = $url . $path;

        foreach (array_keys($thumbs) as $size) {
            $data[$size] = $url . $this->getThumbLocation($path, $size);
        }

        return $data;
    }

    /**
     * 写入文件
     *
     * @param  string          $path
     * @param  StreamInterface $stream
     * @param  array           $thumbs
     * @return bool
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        ftp_put($this->connection, $location, $stream->getMetadata('uri'), FTP_BINARY);

        $this->crop($stream, $thumbs, $location, function ($pathTmp, $cropLocation) {
            ftp_put($this->connection, $cropLocation, $pathTmp, FTP_BINARY);
        });

        return true;
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return bool
     */
    public function delete(string $path, array $thumbs): bool
    {
        $location = $this->applyPathPrefix($path);

        @ftp_delete($this->connection, $location);

        foreach (array_keys($thumbs) as $size) {
            @ftp_delete($this->connection, $this->getThumbLocation($location, $size));
        }

        return true;
    }

    /**
     * 析构方法，断开 FTP 连接
     */
    public function __destruct()
    {
        @ftp_close($this->connection);
    }
}
