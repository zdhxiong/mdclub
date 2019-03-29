<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;
use App\Traits\Url;
use Psr\Http\Message\StreamInterface;

/**
 * SFTP 适配器
 *
 * todo 无法使用
 *
 * Class Sftp
 * @package App\Library\StorageAdapter
 */
class Sftp extends AbstractAdapter implements StorageInterface
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
     * Sftp constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        if (!extension_loaded('ssh2')) {
            throw new \Exception('PHP extension ssh2 is not loaded');
        }

        $this->setPathPrefix();

        [
            'storage_sftp_username' => $username,
            'storage_sftp_password' => $password,
            'storage_sftp_host' => $host,
            'storage_sftp_port' => $port,
        ] = $container->optionService->getMultiple();

        if (!$this->connection = @ssh2_connect($host, (int)$port)) {
            throw new \Exception("Could not connect to SSH2 Server");
        }

        if (!@ssh2_auth_password($this->connection, $username, $password)) {
            throw new \Exception('Could not authenticate to SSH2 Server');
        }

        if (!$this->sftp = @ssh2_sftp($this->connection)) {
            throw new \Exception('Could not initialize SFTP subsystem');
        }
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        $prefix = $this->container->optionService->storage_sftp_root;

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
     * 获取图片 URL
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return array
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

        ssh2_scp_send($this->connection, $stream->getMetadata('uri'), $location);

        $this->crop($stream, $thumbs, $location, function ($pathTmp, $cropLocation) {
            ssh2_scp_send($this->connection, $pathTmp, $cropLocation);
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

        @ssh2_sftp_unlink($this->connection, $location);

        foreach (array_keys($thumbs) as $size) {
            @ssh2_sftp_unlink($this->connection, $this->getThumbLocation($location, $size));
        }

        return true;
    }

    /**
     * 析构方法，断开 SSH 连接
     */
    public function __destruct()
    {
        ssh2_disconnect($this->connection);
    }
}
