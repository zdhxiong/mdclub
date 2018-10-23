<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Class OptionService
 *
 * @package App\Service
 */
class OptionService extends Service
{
    /**
     * 所有字段
     *
     * @var array
     */
    public $allNames = [
        'cache_memcached_host',
        'cache_memcached_password',
        'cache_memcached_port',
        'cache_memcached_username',
        'cache_redis_host',
        'cache_redis_password',
        'cache_redis_port',
        'cache_redis_username',
        'cache_type',          // 缓存类型
        'enable_markdown',     // 是否启用 markdown 支持
        'language',            // 系统语言
        'site_description',    // 站点简介
        'site_gongan_beian',   // 站点公安备案号
        'site_icp_beian',      // 站点 ICP 备案号
        'site_keywords',       // 站点关键词
        'site_name',           // 站点名称
        'site_static_url',     // static 目录资源 URL 地址（以 / 结尾）
        'smtp_host',           // SMTP 服务器地址
        'smtp_password',       // SMTP 密码
        'smtp_port',           // SMTP 端口
        'smtp_reply_to',       // SMTP 邮件回复邮箱
        'smtp_secure',         // SMTP 是否使用 ssl 连接
        'smtp_username',       // SMTP 账户
        'storage_aliyun_oss_access_id',
        'storage_aliyun_oss_access_secret',
        'storage_aliyun_oss_bucket',
        'storage_aliyun_oss_endpoint',
        'storage_ftp_host',
        'storage_ftp_passive',
        'storage_ftp_password',
        'storage_ftp_port',
        'storage_ftp_root',
        'storage_ftp_ssl',
        'storage_ftp_timeout',
        'storage_ftp_username',
        'storage_local_dir',   // 本地文件存储目录（以 / 结尾）
        'storage_qiniu_access_id',
        'storage_qiniu_access_secret',
        'storage_qiniu_bucket',
        'storage_qiniu_endpoint',
        'storage_type',        // 存储类型
        'storage_upyun_bucket',
        'storage_upyun_endpoint',
        'storage_upyun_operator',
        'storage_upyun_password',
        'storage_url',         // 本地文件存储的访问链接（以 / 结尾）
        'theme',               // 主题名称
    ];

    /**
     * 公共字段
     *
     * NOTE: 普通用户只能获取这些字段，仅管理员可以获取和更新所有字段
     *
     * @var array
     */
    public $publicNames = [
        'language',
        'site_description',
        'site_gongan_beian',
        'site_icp_beian',
        'site_keywords',
        'site_name',
    ];

    /**
     * 保存的设置值
     *
     * @var array
     */
    private $options;

    /**
     * 获取所有设置
     *
     * @return array
     */
    public function getAll(): array
    {
        if (is_null($this->options)) {
            $result = $this->filesystemCache->get('options');

            if (is_null($result)) {
                $result = $this->optionModel->select();
                $result = array_combine(
                    array_column($result, 'name'),
                    array_column($result, 'value')
                );

                $this->filesystemCache->set('options', $result);
            }

            $this->options = $result;
        }

        return $this->options;
    }

    /**
     * 获取某一项设置
     *
     * @param  string $name
     * @return string
     * @throws \Exception
     */
    public function get(string $name): string {
        if (!in_array($name, $this->allNames)) {
            throw new \Exception('不存在指定的设置项：' . $name);
        }

        return $this->getAll()[$name];
    }

    /**
     * 更新多个设置
     *
     * @param  array $data
     * @return bool
     */
    public function setMultiple(array $data): bool
    {
        if (empty($data)) {
            return true;
        }

        foreach ($data as $name => $value) {
            if (!in_array($name, $this->allNames)) {
                throw new \Exception('不存在指定的设置项：' . $name);
            }
        }

        foreach ($data as $name => $value) {
            $this->optionModel->where(['name' => $name])->update(['value' => $value]);
        }

        $this->filesystemCache->delete('options');
        $this->options = null;

        return true;
    }

    /**
     * 更新某一项设置
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function set(string $name, string $value): bool
    {
        if (!in_array($name, $this->allNames)) {
            throw new \Exception('不存在指定的设置项：' . $name);
        }

        $this->optionModel->where(['name' => $name])->update(['value' => $value]);
        $this->filesystemCache->delete('options');
        $this->options = null;

        return true;
    }
}
