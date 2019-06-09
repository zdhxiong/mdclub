<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\SystemException;
use App\Traits\fetchCollection;
use Psr\Container\ContainerInterface;
use Tightenco\Collect\Support\Collection;

/**
 * 配置
 *
 * @property string  answer_can_delete
 * @property string  answer_can_delete_before
 * @property string  answer_can_delete_only_no_comment
 * @property string  answer_can_edit
 * @property string  answer_can_edit_before
 * @property string  answer_can_edit_only_no_comment
 * @property string  article_can_delete
 * @property string  article_can_delete_before
 * @property string  article_can_delete_only_no_comment
 * @property string  article_can_edit
 * @property string  article_can_edit_before
 * @property string  article_can_edit_only_no_comment
 * @property string  cache_memcached_host
 * @property string  cache_memcached_password
 * @property string  cache_memcached_port
 * @property string  cache_memcached_username
 * @property string  cache_prefix
 * @property string  cache_redis_host
 * @property string  cache_redis_password
 * @property string  cache_redis_port
 * @property string  cache_redis_username
 * @property string  cache_type
 * @property string  comment_can_delete
 * @property string  comment_can_delete_before
 * @property string  comment_can_edit
 * @property string  comment_can_edit_before
 * @property string  language
 * @property string  question_can_delete
 * @property string  question_can_delete_before
 * @property string  question_can_delete_only_no_answer
 * @property string  question_can_delete_only_no_comment
 * @property string  question_can_edit
 * @property string  question_can_edit_before
 * @property string  question_can_edit_only_no_answer
 * @property string  question_can_edit_only_no_comment
 * @property string  site_description
 * @property string  site_gongan_beian
 * @property string  site_icp_beian
 * @property string  site_keywords
 * @property string  site_name
 * @property string  site_static_url
 * @property string  smtp_host
 * @property string  smtp_password
 * @property string  smtp_port
 * @property string  smtp_reply_to
 * @property string  smtp_secure
 * @property string  smtp_username
 * @property string  storage_aliyun_access_id
 * @property string  storage_aliyun_access_secret
 * @property string  storage_aliyun_bucket
 * @property string  storage_aliyun_endpoint
 * @property string  storage_ftp_host
 * @property string  storage_ftp_passive
 * @property string  storage_ftp_password
 * @property string  storage_ftp_port
 * @property string  storage_ftp_root
 * @property string  storage_ftp_ssl
 * @property string  storage_ftp_username
 * @property string  storage_local_dir
 * @property string  storage_qiniu_access_id
 * @property string  storage_qiniu_access_secret
 * @property string  storage_qiniu_bucket
 * @property string  storage_qiniu_zone
 * @property string  storage_sftp_host
 * @property string  storage_sftp_password
 * @property string  storage_sftp_port
 * @property string  storage_sftp_root
 * @property string  storage_sftp_username
 * @property string  storage_type
 * @property string  storage_upyun_bucket
 * @property string  storage_upyun_operator
 * @property string  storage_upyun_password
 * @property string  storage_url
 * @property string  theme
 *
 * @property-read \App\Model\Option  $optionModel
 * @property-read Role               $roleService
 */
class Option
{
    use fetchCollection;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 公共字段
     *
     * NOTE: 普通用户只能获取这些字段，仅管理员可以获取和更新所有字段
     *
     * @var array
     */
    private static $publicNames = [
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
     * @var Collection
     */
    private $options;

    /**
     * 是否仅获取用户有权限访问的字段
     *
     * @var bool
     */
    private $onlyAuthorized = false;

    /**
     * 是否仅获取用户有权限访问的字段
     *
     * @return Option
     */
    public function onlyAuthorized(): self
    {
        $this->onlyAuthorized = true;

        return $this;
    }

    /**
     * 获取所有配置项的值
     *
     * @return array|Collection
     */
    public function getMultiple()
    {
        if ($this->options === null) {
            $this->options = $this->optionModel
                ->fetchCollection()
                ->select()
                ->pluck('value', 'name');
        }

        $options = $this->options;

        if ($this->onlyAuthorized && !$this->roleService->managerId()) {
            $this->onlyAuthorized = false;
            $options =  $options->only(self::$publicNames);
        }

        return $this->returnArray($options);
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
            throw new SystemException('不存在指定的设置项：' . $name);
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

        if ($diffKeys = collect($data)->diffKeys($this->getMultiple())->keys()->implode(', ')) {
            throw new SystemException('不存在指定的设置项：' . $diffKeys);
        }

        foreach ($data as $name => $value) {
            $this->optionModel
                ->where('name', $name)
                ->update('value', $value);
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
        $this->setMultiple([$name => $value]);
    }

    /********************************************************************************
     * 魔术方法
     *******************************************************************************/

    /**
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name === 'optionModel') {
            return $this->container->get('optionModel');
        }

        if ($name === 'roleService') {
            return $this->container->get('roleService');
        }

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

    /**
     * @param  string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->fetchCollection()->getMultiple()->has($name);
    }
}
