import { getRequest, patchRequest } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import { OptionResponse, OptionUpdateRequestBody } from './models';

/**
 * 获取站点全局设置参数
 * 获取站点全局设置参数
 */
export const get = (): Promise<OptionResponse> =>
  getRequest(buildURL('/options', {}));

/**
 * 🔐更新站点全局设置
 * 仅管理员可调用该接口
 */
export const update = (
  params: OptionUpdateRequestBody,
): Promise<OptionResponse> =>
  patchRequest(
    buildURL('/options', params),
    buildRequestBody(params, [
      'answer_can_delete',
      'answer_can_delete_before',
      'answer_can_delete_only_no_comment',
      'answer_can_edit',
      'answer_can_edit_before',
      'answer_can_edit_only_no_comment',
      'article_can_delete',
      'article_can_delete_before',
      'article_can_delete_only_no_comment',
      'article_can_edit',
      'article_can_edit_before',
      'article_can_edit_only_no_comment',
      'cache_memcached_host',
      'cache_memcached_password',
      'cache_memcached_port',
      'cache_memcached_username',
      'cache_prefix',
      'cache_redis_host',
      'cache_redis_password',
      'cache_redis_port',
      'cache_redis_username',
      'cache_type',
      'comment_can_delete',
      'comment_can_delete_before',
      'comment_can_edit',
      'comment_can_edit_before',
      'language',
      'question_can_delete',
      'question_can_delete_before',
      'question_can_delete_only_no_answer',
      'question_can_delete_only_no_comment',
      'question_can_edit',
      'question_can_edit_before',
      'question_can_edit_only_no_answer',
      'question_can_edit_only_no_comment',
      'search_third',
      'search_type',
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
      'storage_aliyun_access_id',
      'storage_aliyun_access_secret',
      'storage_aliyun_bucket',
      'storage_aliyun_dir',
      'storage_aliyun_endpoint',
      'storage_ftp_host',
      'storage_ftp_passive',
      'storage_ftp_password',
      'storage_ftp_port',
      'storage_ftp_dir',
      'storage_ftp_ssl',
      'storage_ftp_username',
      'storage_local_dir',
      'storage_qiniu_access_id',
      'storage_qiniu_access_secret',
      'storage_qiniu_bucket',
      'storage_qiniu_dir',
      'storage_qiniu_zone',
      'storage_sftp_host',
      'storage_sftp_password',
      'storage_sftp_port',
      'storage_sftp_dir',
      'storage_sftp_username',
      'storage_type',
      'storage_upyun_bucket',
      'storage_upyun_dir',
      'storage_upyun_operator',
      'storage_upyun_password',
      'storage_url',
      'theme',
    ]),
  );
