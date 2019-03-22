<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ContainerAbstracts;

/**
 * Class Option
 *
 * @property string cache_memcached_host
 * @property string cache_memcached_password
 * @property string cache_memcached_port
 * @property string cache_memcached_username
 * @property string cache_prefix
 * @property string cache_redis_host
 * @property string cache_redis_password
 * @property string cache_redis_port
 * @property string cache_redis_username
 * @property string cache_type
 * @property string language
 * @property string site_description
 * @property string site_gongan_beian
 * @property string site_icp_beian
 * @property string site_keywords
 * @property string site_name
 * @property string site_static_url
 * @property string smtp_host
 * @property string smtp_password
 * @property string smtp_port
 * @property string smtp_reply_to
 * @property string smtp_secure
 * @property string smtp_username
 * @property string storage_aliyun_access_id
 * @property string storage_aliyun_access_secret
 * @property string storage_aliyun_bucket
 * @property string storage_aliyun_endpoint
 * @property string storage_ftp_host
 * @property string storage_ftp_password
 * @property string storage_ftp_port
 * @property string storage_ftp_root
 * @property string storage_ftp_ssl
 * @property string storage_ftp_username
 * @property string storage_local_dir
 * @property string storage_qiniu_access_id
 * @property string storage_qiniu_access_secret
 * @property string storage_qiniu_bucket
 * @property string storage_qiniu_endpoint
 * @property string storage_type
 * @property string storage_upyun_bucket
 * @property string storage_upyun_endpoint
 * @property string storage_upyun_operator
 * @property string storage_upyun_password
 * @property string storage_url
 * @property string theme
 *
 * @package App\Service
 */
class Option extends ContainerAbstracts
{
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
     * Option constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container);
    }

    /**
     * 获取所有配置项的值
     *
     * @return array
     */
    public function getMultiple(): array
    {
        if (is_null($this->options)) {
            $result = $this->container->optionModel->select();
            $result = array_combine(
                array_column($result, 'name'),
                array_column($result, 'value')
            );

            $this->options = $result;
        }

        return $this->options;
    }

    /**
     * 获取指定配置项的值
     *
     * @param  string $name
     * @return string
     */
    public function get(string $name): string
    {
        $options = $this->getMultiple();

        if (!isset($options[$name])) {
            throw new \Exception('不存在指定的设置项：' . $name);
        }

        return $options[$name];
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

        $options = $this->getMultiple();

        foreach ($data as $name => $value) {
            if (!isset($options[$name])) {
                throw new \Exception('不存在指定的设置项：' . $name);
            }
        }

        foreach ($data as $name => $value) {
            $this->container->optionModel->where(['name' => $name])->update(['value' => $value]);
        }

        $this->options = null;
    }

    /**
     * 更新某一项设置
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value): void
    {
        $options = $this->getMultiple();

        if (!isset($options[$name])) {
            throw new \Exception('不存在指定的设置项：' . $name);
        }

        $this->container->optionModel->where(['name' => $name])->update(['value' => $value]);
        $this->options = null;
    }

    /********************************************************************************
     * 魔术方法
     *******************************************************************************/

    /**
     * @param  string $name
     * @return string
     */
    public function __get(string $name): string
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }
}
