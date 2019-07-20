<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use MDClub\Exception\SystemException;
use MDClub\Traits\Url;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * SFTP 适配器
 *
 * todo 无法使用
 */
class Sftp extends Abstracts implements Interfaces
{
    use Url;

    /**
     * SSH2 连接 resource
     *
     * @var resource
     */
    protected $connection;

    /**
     * SFTP resource
     *
     * @var resource
     */
    protected $sftp;
    /**
     * 存储路径
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        if (!extension_loaded('ssh2')) {
            throw new SystemException('PHP extension ssh2 is not loaded');
        }

        $this->setPathPrefix();

        $username = $this->option->storage_sftp_username;
        $password = $this->option->storage_sftp_password;
        $host = $this->option->storage_sftp_host;
        $port = $this->option->storage_sftp_port;

        if (!$this->connection = @ssh2_connect($host, (int)$port)) {
            throw new SystemException("Could not connect to SSH2 Server");
        }

        if (!@ssh2_auth_password($this->connection, $username, $password)) {
            throw new SystemException('Could not authenticate to SSH2 Server');
        }

        if (!$this->sftp = @ssh2_sftp($this->connection)) {
            throw new SystemException('Could not initialize SFTP subsystem');
        }
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = $this->option->storage_sftp_root;

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

    }

    /**
     * @inheritDoc
     */
    public function get(string $path, array $thumbs): array
    {
        $url = $this->getStorageUrl();
        $data['o'] = $url . $path;

        foreach (array_keys($thumbs) as $size) {
            $data[$size] = $url . $this->getThumbLocation($path, $size);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        ssh2_scp_send($this->connection, $stream->getMetadata('uri'), $location);

        $this->crop($stream, $thumbs, $location, function ($pathTmp, $cropLocation) {
            ssh2_scp_send($this->connection, $pathTmp, $cropLocation);
        });
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);

        @ssh2_sftp_unlink($this->connection, $location);

        foreach (array_keys($thumbs) as $size) {
            @ssh2_sftp_unlink($this->connection, $this->getThumbLocation($location, $size));
        }
    }

    /**
     * 析构方法，断开 SSH 连接
     */
    public function __destruct()
    {
        ssh2_disconnect($this->connection);
    }
}
