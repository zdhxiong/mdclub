<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Abstracts\ContainerAbstracts;
use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;

/**
 * 数据库中删除 storage_ftp_passive、storage_ftp_ssl，新增 storage_ftp_ftps（是否是 ftps）
 *
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
    }

    /**
     * 设置文件存储路径
     */
    protected function setPathPrefix(): void
    {
        [
            'storage_ftp_ftps' => $ftps,
            'storage_ftp_username' => $username,
            'storage_ftp_password' => $password,
            'storage_ftp_host' => $host,
            'storage_ftp_port' => $port,
            'storage_ftp_root' => $root,
        ] = $this->container->optionService->getMultiple();

        $protocol = $ftps ? 'ftps://' : 'ftp://';

        // 移除最前面的 /，在最后添加 /
        if ($root) {
            $root = ltrim($root, '\\/');

            if (!in_array(substr($root, -1), ['/', '\\'])) {
                $root .= '/';
            }
        }

        $this->pathPrefix = "{$protocol}{$username}:{$password}@{$host}:{$port}/{$root}";
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
        if (!is_dir($root)) {
            $umask = umask(0);

            if (!@mkdir($root, 0755, true)) {
                $mkdirError = error_get_last();
            }

            umask($umask);

            if (!is_dir($root)) {
                $errorMessage = $mkdirError['message'] ?? '';
                throw new \Exception(sprintf('Impossible to create the root directory "%s". %s', $root, $errorMessage));
            }
        }
    }

    /**
     * 写入文件
     *
     * @param  string $path
     * @param  string $content
     * @return bool
     */
    public function write(string $path, string $content): bool
    {
        $location = $this->applyPathPrefix($path);
        // todo 测试 ftp/ftps 是否会自动创建目录
        $this->ensureDirectory(dirname($location));

        return !!file_put_contents($location, $content);
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

        return @unlink($location);
    }

    public function deleteMultiple(array $paths): bool
    {
        // TODO: Implement deleteMultiple() method.
    }
}
