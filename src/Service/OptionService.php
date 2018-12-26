<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;

/**
 * @property-read \App\Model\OptionModel      currentModel
 *
 * Class OptionService
 * @package App\Service
 */
class OptionService extends ServiceAbstracts
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
        'cache_type',
        'language',
        'site_description',
        'site_gongan_beian',
        'site_icp_beian',
        'site_keywords',
        'site_name',
        'site_static_url',
        'smtp_host',
        'smtp_password',
        'smtp_port',
        'smtp_reply_to',
        'smtp_secure',
        'smtp_username',
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
        'storage_local_dir',
        'storage_qiniu_access_id',
        'storage_qiniu_access_secret',
        'storage_qiniu_bucket',
        'storage_qiniu_endpoint',
        'storage_type',
        'storage_upyun_bucket',
        'storage_upyun_endpoint',
        'storage_upyun_operator',
        'storage_upyun_password',
        'storage_url',
        'theme',
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
            $result = $this->fileCache->get('options');

            if (is_null($result)) {
                $result = $this->optionModel->select();
                $result = array_combine(
                    array_column($result, 'name'),
                    array_column($result, 'value')
                );

                $this->fileCache->set('options', $result);
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
    public function get(string $name): string
    {
        if (!in_array($name, $this->allNames)) {
            throw new \Exception('不存在指定的设置项：' . $name);
        }

        return $this->getAll()[$name];
    }

    /**
     * 更新多个设置
     *
     * @param  array $data
     */
    public function setMultiple(array $data): void
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $name => $value) {
            if (!in_array($name, $this->allNames)) {
                throw new \Exception('不存在指定的设置项：' . $name);
            }
        }

        foreach ($data as $name => $value) {
            $this->optionModel->where(['name' => $name])->update(['value' => $value]);
        }

        $this->fileCache->delete('options');
        $this->options = null;
    }

    /**
     * 更新某一项设置
     *
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void
    {
        if (!in_array($name, $this->allNames)) {
            throw new \Exception('不存在指定的设置项：' . $name);
        }

        $this->optionModel->where(['name' => $name])->update(['value' => $value]);
        $this->fileCache->delete('options');
        $this->options = null;
    }
}
