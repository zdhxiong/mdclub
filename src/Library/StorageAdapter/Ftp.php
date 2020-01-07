<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use MDClub\Constant\OptionConstant;
use MDClub\Exception\SystemException;
use MDClub\Facade\Library\Option;
use MDClub\Helper\Url;
use Psr\Http\Message\StreamInterface;

/**
 * FTP/FTPS 适配器
 *
 * NOTE: FTPS 需要开启 openssl 扩展
 */
class Ftp extends Abstracts implements Interfaces
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

    public function __construct()
    {
        if (!extension_loaded('ftp')) {
            throw new SystemException('PHP extension FTP is not loaded.');
        }

        $this->setPathPrefix();

        $username = Option::get(OptionConstant::STORAGE_FTP_USERNAME);
        $password = Option::get(OptionConstant::STORAGE_FTP_PASSWORD);
        $host = Option::get(OptionConstant::STORAGE_FTP_HOST);
        $port = Option::get(OptionConstant::STORAGE_FTP_PORT);
        $ssl = Option::get(OptionConstant::STORAGE_FTP_SSL);
        $passive = Option::get(OptionConstant::STORAGE_FTP_PASSIVE);

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
        $prefix = Option::get(OptionConstant::STORAGE_FTP_ROOT);

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
     * @inheritDoc
     */
    public function get(string $path, array $thumbs): array
    {
        $storagePath = Url::storagePath();
        $data['o'] = $storagePath . $path;

        foreach (array_keys($thumbs) as $size) {
            $data[$size] = $storagePath . $this->getThumbLocation($path, $size);
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

        ftp_put($this->connection, $location, $stream->getMetadata('uri'), FTP_BINARY);

        $this->crop(
            $stream,
            $thumbs,
            $location,
            /**
             * @param string $pathTmp      缩略图临时文件路径
             * @param string $cropLocation 缩略图将要保存的路径
             */
            function ($pathTmp, $cropLocation) {
                ftp_put($this->connection, $cropLocation, $pathTmp, FTP_BINARY);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);

        @ftp_delete($this->connection, $location);

        foreach (array_keys($thumbs) as $size) {
            @ftp_delete($this->connection, $this->getThumbLocation($location, $size));
        }
    }

    /**
     * 析构方法，断开 FTP 连接
     */
    public function __destruct()
    {
        @ftp_close($this->connection);
    }
}
