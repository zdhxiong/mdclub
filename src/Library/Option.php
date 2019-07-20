<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Exception\SystemException;
use MDClub\Model\Option as OptionModel;
use Psr\Container\ContainerInterface;

/**
 * 配置项
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
 */
class Option
{
    /**
     * @var OptionModel
     */
    protected $model;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * 公共字段
     *
     * NOTE: 普通用户只能获取这些字段，仅管理员可以获取和更新所有字段
     *
     * @var array
     */
    protected static $publicNames = [
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
    protected $options;

    /**
     * 是否仅获取用户有权限访问的字段
     *
     * @var bool
     */
    protected $onlyAuthorized = false;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->model = $container->get('optionModel');
        $this->auth = $container->get('auth');
    }

    public function __get(string $name)
    {
        $options = $this->all();

        if (!isset($options[$name])) {
            throw new SystemException('不存在指定的设置项：' . $name);
        }

        return $options[$name];
    }

    public function __set(string $name, $value): void
    {
        $this->set([$name => $value]);
    }

    public function __isset(string $name): bool
    {
        return isset($this->all()[$name]);
    }

    /**
     * 是否仅获取用户有权限访问的字段
     *
     * @return Option
     */
    public function onlyAuthorized(): Option
    {
        $this->onlyAuthorized = true;

        return $this;
    }

    /**
     * 获取所有配置项的值
     *
     * @return array
     */
    public function all(): array
    {
        if ($this->options === null) {
            $this->options = $this->model->pluck('value', 'name');
        }

        $options = $this->options;

        if ($this->onlyAuthorized && !$this->auth->isManager()) {
            $this->onlyAuthorized = false;
            $options = collect($options)->only(self::$publicNames)->all();
        }

        return $options;
    }

    /**
     * 更新多个设置
     *
     * @param  array           $data
     * @throws SystemException
     */
    public function set(array $data): void
    {
        if (empty($data)) {
            return;
        }

        if ($diffKeys = collect($data)->diffKeys($this->all())->keys()->implode(', ')) {
            throw new SystemException('不存在指定的设置项：' . $diffKeys);
        }

        foreach ($data as $name => $value) {
            $this->model->where('name', $name)->update('value', $value);
        }

        $this->options = null;
    }
}
