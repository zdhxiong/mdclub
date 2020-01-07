<?php

declare(strict_types=1);

namespace MDClub\Constant;

/**
 * 配置项名称常量
 */
class OptionConstant
{
    public const ANSWER_CAN_DELETE = 'answer_can_delete';
    public const ANSWER_CAN_DELETE_BEFORE = 'answer_can_delete_before';
    public const ANSWER_CAN_DELETE_ONLY_NO_COMMENT = 'answer_can_delete_only_no_comment';
    public const ANSWER_CAN_EDIT = 'answer_can_edit';
    public const ANSWER_CAN_EDIT_BEFORE = 'answer_can_edit_before';
    public const ANSWER_CAN_EDIT_ONLY_NO_COMMENT = 'answer_can_edit_only_no_comment';
    public const ARTICLE_CAN_DELETE = 'article_can_delete';
    public const ARTICLE_CAN_DELETE_BEFORE = 'article_can_delete_before';
    public const ARTICLE_CAN_DELETE_ONLY_NO_COMMENT = 'article_can_delete_only_no_comment';
    public const ARTICLE_CAN_EDIT = 'article_can_edit';
    public const ARTICLE_CAN_EDIT_BEFORE = 'article_can_edit_before';
    public const ARTICLE_CAN_EDIT_ONLY_NO_COMMENT = 'article_can_edit_only_no_comment';
    public const CACHE_MEMCACHED_HOST = 'cache_memcached_host';
    public const CACHE_MEMCACHED_PASSWORD = 'cache_memcached_password';
    public const CACHE_MEMCACHED_PORT = 'cache_memcached_port';
    public const CACHE_MEMCACHED_USERNAME = 'cache_memcached_username';
    public const CACHE_PREFIX = 'cache_prefix';
    public const CACHE_REDIS_HOST = 'cache_redis_host';
    public const CACHE_REDIS_PASSWORD = 'cache_redis_password';
    public const CACHE_REDIS_PORT = 'cache_redis_port';
    public const CACHE_REDIS_USERNAME = 'cache_redis_username';
    public const CACHE_TYPE = 'cache_type';
    public const COMMENT_CAN_DELETE = 'comment_can_delete';
    public const COMMENT_CAN_DELETE_BEFORE = 'comment_can_delete_before';
    public const COMMENT_CAN_EDIT = 'comment_can_edit';
    public const COMMENT_CAN_EDIT_BEFORE = 'comment_can_edit_before';
    public const LANGUAGE = 'language';
    public const QUESTION_CAN_DELETE = 'question_can_delete';
    public const QUESTION_CAN_DELETE_BEFORE = 'question_can_delete_before';
    public const QUESTION_CAN_DELETE_ONLY_NO_ANSWER = 'question_can_delete_only_no_answer';
    public const QUESTION_CAN_DELETE_ONLY_NO_COMMENT = 'question_can_delete_only_no_comment';
    public const QUESTION_CAN_EDIT = 'question_can_edit';
    public const QUESTION_CAN_EDIT_BEFORE = 'question_can_edit_before';
    public const QUESTION_CAN_EDIT_ONLY_NO_ANSWER = 'question_can_edit_only_no_answer';
    public const QUESTION_CAN_EDIT_ONLY_NO_COMMENT = 'question_can_edit_only_no_comment';
    public const SITE_DESCRIPTION = 'site_description';
    public const SITE_GONGAN_BEIAN = 'site_gongan_beian';
    public const SITE_ICP_BEIAN = 'site_icp_beian';
    public const SITE_KEYWORDS = 'site_keywords';
    public const SITE_NAME = 'site_name';
    public const SITE_STATIC_URL = 'site_static_url';
    public const SMTP_HOST = 'smtp_host';
    public const SMTP_PASSWORD = 'smtp_password';
    public const SMTP_PORT = 'smtp_port';
    public const SMTP_REPLY_TO = 'smtp_reply_to';
    public const SMTP_SECURE = 'smtp_secure';
    public const SMTP_USERNAME = 'smtp_username';
    public const STORAGE_ALIYUN_ACCESS_ID = 'storage_aliyun_access_id';
    public const STORAGE_ALIYUN_ACCESS_SECRET = 'storage_aliyun_access_secret';
    public const STORAGE_ALIYUN_BUCKET = 'storage_aliyun_bucket';
    public const STORAGE_ALIYUN_ENDPOINT = 'storage_aliyun_endpoint';
    public const STORAGE_FTP_HOST = 'storage_ftp_host';
    public const STORAGE_FTP_PASSIVE = 'storage_ftp_passive';
    public const STORAGE_FTP_PASSWORD = 'storage_ftp_password';
    public const STORAGE_FTP_PORT = 'storage_ftp_port';
    public const STORAGE_FTP_ROOT = 'storage_ftp_root';
    public const STORAGE_FTP_SSL = 'storage_ftp_ssl';
    public const STORAGE_FTP_USERNAME = 'storage_ftp_username';
    public const STORAGE_LOCAL_DIR = 'storage_local_dir';
    public const STORAGE_QINIU_ACCESS_ID = 'storage_qiniu_access_id';
    public const STORAGE_QINIU_ACCESS_SECRET = 'storage_qiniu_access_secret';
    public const STORAGE_QINIU_BUCKET = 'storage_qiniu_bucket';
    public const STORAGE_QINIU_ZONE = 'storage_qiniu_zone';
    public const STORAGE_SFTP_HOST = 'storage_sftp_host';
    public const STORAGE_SFTP_PASSWORD = 'storage_sftp_password';
    public const STORAGE_SFTP_PORT = 'storage_sftp_port';
    public const STORAGE_SFTP_ROOT = 'storage_sftp_root';
    public const STORAGE_SFTP_USERNAME = 'storage_sftp_username';
    public const STORAGE_TYPE = 'storage_type';
    public const STORAGE_UPYUN_BUCKET = 'storage_upyun_bucket';
    public const STORAGE_UPYUN_OPERATOR = 'storage_upyun_operator';
    public const STORAGE_UPYUN_PASSWORD = 'storage_upyun_password';
    public const STORAGE_URL = 'storage_url';
    public const THEME = 'theme';

    /**
     * 公共字段
     *
     * NOTE: 普通用户只能获取这些字段，仅管理员可以获取和更新所有字段
     */
    public const PUBLIC_NAMES = [
        self::ANSWER_CAN_DELETE,
        self::ANSWER_CAN_DELETE_BEFORE,
        self::ANSWER_CAN_DELETE_ONLY_NO_COMMENT,
        self::ANSWER_CAN_EDIT,
        self::ANSWER_CAN_EDIT_BEFORE,
        self::ANSWER_CAN_EDIT_ONLY_NO_COMMENT,
        self::ARTICLE_CAN_DELETE,
        self::ARTICLE_CAN_DELETE_BEFORE,
        self::ARTICLE_CAN_DELETE_ONLY_NO_COMMENT,
        self::ARTICLE_CAN_EDIT,
        self::ARTICLE_CAN_EDIT_BEFORE,
        self::ARTICLE_CAN_EDIT_ONLY_NO_COMMENT,
        self::COMMENT_CAN_DELETE,
        self::COMMENT_CAN_DELETE_BEFORE,
        self::COMMENT_CAN_EDIT,
        self::COMMENT_CAN_EDIT_BEFORE,
        self::LANGUAGE,
        self::QUESTION_CAN_DELETE,
        self::QUESTION_CAN_DELETE_BEFORE,
        self::QUESTION_CAN_DELETE_ONLY_NO_ANSWER,
        self::QUESTION_CAN_DELETE_ONLY_NO_COMMENT,
        self::QUESTION_CAN_EDIT,
        self::QUESTION_CAN_EDIT_BEFORE,
        self::QUESTION_CAN_EDIT_ONLY_NO_ANSWER,
        self::QUESTION_CAN_EDIT_ONLY_NO_COMMENT,
        self::SITE_DESCRIPTION,
        self::SITE_GONGAN_BEIAN,
        self::SITE_ICP_BEIAN,
        self::SITE_KEYWORDS,
        self::SITE_NAME,
    ];

    /**
     * 值为 boolean 的字段，在存入和读取时进行转换
     */
    public const BOOLEAN_NAMES = [
        self::ANSWER_CAN_DELETE,
        self::ANSWER_CAN_DELETE_ONLY_NO_COMMENT,
        self::ANSWER_CAN_EDIT,
        self::ANSWER_CAN_EDIT_ONLY_NO_COMMENT,
        self::ARTICLE_CAN_DELETE,
        self::ARTICLE_CAN_DELETE_ONLY_NO_COMMENT,
        self::ARTICLE_CAN_EDIT,
        self::ARTICLE_CAN_EDIT_ONLY_NO_COMMENT,
        self::COMMENT_CAN_DELETE,
        self::COMMENT_CAN_EDIT,
        self::QUESTION_CAN_DELETE,
        self::QUESTION_CAN_DELETE_ONLY_NO_ANSWER,
        self::QUESTION_CAN_DELETE_ONLY_NO_COMMENT,
        self::QUESTION_CAN_EDIT,
        self::QUESTION_CAN_EDIT_ONLY_NO_ANSWER,
        self::QUESTION_CAN_EDIT_ONLY_NO_COMMENT,
        self::STORAGE_FTP_PASSIVE,
        self::STORAGE_FTP_SSL,
    ];

    /**
     * 值为 integer 的字段，在读取时进行转换
     */
    public const INTEGER_NAMES = [
        self::ANSWER_CAN_DELETE_BEFORE,
        self::ANSWER_CAN_EDIT_BEFORE,
        self::ARTICLE_CAN_DELETE_BEFORE,
        self::ARTICLE_CAN_EDIT_BEFORE,
        self::ARTICLE_CAN_EDIT_BEFORE,
        self::CACHE_MEMCACHED_PORT,
        self::CACHE_REDIS_PORT,
        self::COMMENT_CAN_DELETE_BEFORE,
        self::COMMENT_CAN_EDIT_BEFORE,
        self::QUESTION_CAN_DELETE_BEFORE,
        self::QUESTION_CAN_EDIT_BEFORE,
        self::SMTP_PORT,
        self::STORAGE_FTP_PORT,
        self::STORAGE_SFTP_PORT,
    ];
}
